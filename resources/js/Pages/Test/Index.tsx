import React, { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Progress } from '@/Components/ui/progress';
import { RadioGroup, RadioGroupItem } from '@/Components/ui/radio-group';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';

interface Word {
    id: number;
    word: string;
    definition: string;
    example: string;
    topic: string;
    cefr_level: string;
}

interface TestItem {
    id: number;
    word: Word;
    question_type: string;
    options?: string[];
    correct_answer: string;
}

interface DailyTest {
    id: number;
    date: string;
    is_completed: boolean;
    score?: number;
    items: TestItem[];
}

interface TestStats {
    total_tests: number;
    completed_tests: number;
    completion_rate: number;
    average_score: number;
    recent_scores: number[];
    current_streak: number;
}

interface Props {
    test: DailyTest;
    stats: TestStats;
    user: any;
}

export default function TestIndex({ test, stats, user }: Props) {
    const [currentItemIndex, setCurrentItemIndex] = useState(0);
    const [answers, setAnswers] = useState<Record<number, string>>({});
    const [showResults, setShowResults] = useState(false);
    const [submitting, setSubmitting] = useState(false);

    const currentItem = test.items[currentItemIndex];
    const isLastItem = currentItemIndex === test.items.length - 1;
    const progress = ((currentItemIndex + 1) / test.items.length) * 100;

    const handleAnswerChange = (value: string) => {
        setAnswers(prev => ({
            ...prev,
            [currentItem.id]: value
        }));
    };

    const submitAnswer = async () => {
        const answer = answers[currentItem.id];
        if (!answer) return;

        setSubmitting(true);
        try {
            const response = await fetch(route('test.answer'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    daily_test_item_id: currentItem.id,
                    answer: answer,
                    question_type: currentItem.question_type,
                }),
            });

            const data = await response.json();
            
            if (data.success) {
                if (isLastItem) {
                    await completeTest();
                } else {
                    setCurrentItemIndex(prev => prev + 1);
                }
            }
        } catch (error) {
            console.error('Error submitting answer:', error);
        } finally {
            setSubmitting(false);
        }
    };

    const completeTest = async () => {
        try {
            const response = await fetch(route('test.complete'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    test_id: test.id,
                }),
            });

            const data = await response.json();
            
            if (data.success) {
                router.get(route('test.results'));
            }
        } catch (error) {
            console.error('Error completing test:', error);
        }
    };

    const skipToNext = () => {
        if (isLastItem) {
            completeTest();
        } else {
            setCurrentItemIndex(prev => prev + 1);
        }
    };

    const renderQuestion = () => {
        if (!currentItem) return null;

        const questionTypes = {
            'word_to_definition': 'Choose the correct definition:',
            'definition_to_word': 'Type the word for this definition:',
            'word_to_meaning': 'Choose the correct meaning:',
            'meaning_to_word': 'Type the word with this meaning:'
        };

        const questionText = questionTypes[currentItem.question_type as keyof typeof questionTypes];

        return (
            <div className="space-y-6">
                <div className="text-center">
                    <h2 className="text-2xl font-bold mb-2">
                        {currentItem.question_type.includes('word_to') 
                            ? currentItem.word.word 
                            : currentItem.word.definition}
                    </h2>
                    <p className="text-gray-600">{questionText}</p>
                </div>

                {currentItem.options ? (
                    // Multiple choice
                    <RadioGroup 
                        onValueChange={handleAnswerChange}
                        value={answers[currentItem.id] || ''}
                    >
                        <div className="space-y-3">
                            {currentItem.options.map((option, index) => (
                                <div key={index} className="flex items-center space-x-2">
                                    <RadioGroupItem value={option} id={`option-${index}`} />
                                    <Label htmlFor={`option-${index}`} className="cursor-pointer">
                                        {option}
                                    </Label>
                                </div>
                            ))}
                        </div>
                    </RadioGroup>
                ) : (
                    // Text input
                    <div>
                        <Input
                            type="text"
                            placeholder="Type your answer..."
                            value={answers[currentItem.id] || ''}
                            onChange={(e) => handleAnswerChange(e.target.value)}
                            className="text-lg p-4"
                            autoFocus
                        />
                    </div>
                )}
            </div>
        );
    };

    if (test.is_completed) {
        return (
            <AuthenticatedLayout user={user}>
                <Head title="Daily Test - Completed" />
                <div className="py-12">
                    <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                        <Card>
                            <CardContent className="text-center py-12">
                                <h1 className="text-2xl font-bold mb-4">Test Already Completed!</h1>
                                <p className="text-gray-600 mb-6">
                                    You've already completed today's test with a score of {test.score}%.
                                </p>
                                <div className="space-x-4">
                                    <Button onClick={() => router.get(route('test.results'))}>
                                        View Results
                                    </Button>
                                    <Button 
                                        variant="outline" 
                                        onClick={() => router.get(route('learning.index'))}
                                    >
                                        Continue Learning
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    return (
        <AuthenticatedLayout user={user}>
            <Head title="Daily Test" />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    {/* Test Progress */}
                    <Card className="mb-8">
                        <CardContent className="pt-6">
                            <div className="flex justify-between items-center mb-4">
                                <span className="text-sm font-medium">
                                    Question {currentItemIndex + 1} of {test.items.length}
                                </span>
                                <span className="text-sm text-gray-600">
                                    {Math.round(progress)}% Complete
                                </span>
                            </div>
                            <Progress value={progress} className="w-full" />
                        </CardContent>
                    </Card>

                    {/* Current Question */}
                    <Card className="mb-8">
                        <CardContent className="pt-6">
                            {renderQuestion()}
                        </CardContent>
                    </Card>

                    {/* Action Buttons */}
                    <div className="flex justify-between">
                        <Button 
                            variant="outline" 
                            onClick={skipToNext}
                            disabled={submitting}
                        >
                            Skip
                        </Button>

                        <div className="space-x-4">
                            {currentItemIndex > 0 && (
                                <Button 
                                    variant="outline"
                                    onClick={() => setCurrentItemIndex(prev => Math.max(0, prev - 1))}
                                    disabled={submitting}
                                >
                                    Previous
                                </Button>
                            )}
                            
                            <Button 
                                onClick={submitAnswer}
                                disabled={!answers[currentItem.id] || submitting}
                            >
                                {submitting 
                                    ? 'Submitting...' 
                                    : isLastItem 
                                        ? 'Complete Test' 
                                        : 'Next Question'
                                }
                            </Button>
                        </div>
                    </div>

                    {/* Test Stats Sidebar */}
                    <Card className="mt-8">
                        <CardHeader>
                            <CardTitle>Your Test Statistics</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div className="text-center">
                                    <div className="text-2xl font-bold">{stats.total_tests}</div>
                                    <div className="text-sm text-gray-600">Total Tests</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-green-600">{stats.completion_rate}%</div>
                                    <div className="text-sm text-gray-600">Completion Rate</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-blue-600">{stats.average_score}%</div>
                                    <div className="text-sm text-gray-600">Average Score</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-purple-600">{stats.current_streak}</div>
                                    <div className="text-sm text-gray-600">Daily Streak</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}