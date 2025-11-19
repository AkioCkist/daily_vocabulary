import React, { useState } from 'react';
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

interface UserWord {
    id: number;
    word: Word;
    consecutive_correct: number;
    mistake_count: number;
    mastered: boolean;
    last_seen_at: string;
}

interface ReviewProgress {
    total_review_words: number;
    almost_mastered: number;
    struggling_words: number;
    recently_added: number;
    mastery_rate: number;
}

interface Props {
    reviewWords: UserWord[];
    progress: ReviewProgress;
    topicStats: any[];
    user: any;
}

export default function ReviewIndex({ reviewWords, progress, topicStats, user }: Props) {
    return (
        <AuthenticatedLayout user={user}>
            <Head title="Review Words" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Review Progress Overview */}
                    <div className="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">Total Review</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{progress.total_review_words}</div>
                            </CardContent>
                        </Card>
                        
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">Almost Mastered</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-green-600">{progress.almost_mastered}</div>
                            </CardContent>
                        </Card>
                        
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">Struggling</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-red-600">{progress.struggling_words}</div>
                            </CardContent>
                        </Card>
                        
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">Recently Added</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-blue-600">{progress.recently_added}</div>
                            </CardContent>
                        </Card>
                        
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">Mastery Rate</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-purple-600">{progress.mastery_rate}%</div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Action Buttons */}
                    <div className="flex flex-wrap gap-4 mb-8">
                        <Button 
                            onClick={() => router.get(route('review.practice'))}
                            size="lg"
                            disabled={progress.total_review_words === 0}
                        >
                            Start Practice Session
                        </Button>
                        
                        <Button 
                            onClick={() => router.get(route('review.intensive'))}
                            variant="outline"
                            size="lg"
                            disabled={progress.struggling_words === 0}
                        >
                            Intensive Review
                        </Button>
                        
                        <Button 
                            onClick={() => router.get(route('review.spaced-repetition'))}
                            variant="outline"
                            size="lg"
                        >
                            Spaced Repetition
                        </Button>
                    </div>

                    {/* Review Words List */}
                    {reviewWords.length > 0 ? (
                        <Card className="mb-8">
                            <CardHeader>
                                <CardTitle>Words Needing Review</CardTitle>
                                <CardDescription>
                                    Click on any word to practice it individually
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    {reviewWords.slice(0, 10).map((userWord) => (
                                        <div key={userWord.id} className="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                                            <div className="flex-1">
                                                <div className="flex items-center gap-3 mb-2">
                                                    <h3 className="font-semibold text-lg">{userWord.word.word}</h3>
                                                    <Badge variant="secondary">{userWord.word.topic}</Badge>
                                                    <Badge variant="outline">{userWord.word.cefr_level}</Badge>
                                                </div>
                                                <p className="text-gray-600 mb-2">{userWord.word.definition}</p>
                                                <div className="flex gap-4 text-sm text-gray-500">
                                                    <span>Mistakes: {userWord.mistake_count}</span>
                                                    <span>Correct: {userWord.consecutive_correct}</span>
                                                    <span>Last seen: {new Date(userWord.last_seen_at).toLocaleDateString()}</span>
                                                </div>
                                            </div>
                                            <div className="flex gap-2">
                                                <Button 
                                                    variant="outline" 
                                                    size="sm"
                                                    onClick={() => {
                                                        // Individual practice functionality
                                                        console.log('Practice word:', userWord.word.id);
                                                    }}
                                                >
                                                    Practice
                                                </Button>
                                                {userWord.consecutive_correct >= 2 && (
                                                    <Button 
                                                        variant="outline" 
                                                        size="sm"
                                                        onClick={async () => {
                                                            try {
                                                                await fetch(route('review.mark-mastered'), {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'Content-Type': 'application/json',
                                                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                                                    },
                                                                    body: JSON.stringify({
                                                                        word_id: userWord.word.id,
                                                                    }),
                                                                });
                                                                router.reload();
                                                            } catch (error) {
                                                                console.error('Error marking as mastered:', error);
                                                            }
                                                        }}
                                                    >
                                                        Mark Mastered
                                                    </Button>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                
                                {reviewWords.length > 10 && (
                                    <div className="mt-4 text-center text-gray-600">
                                        Showing 10 of {reviewWords.length} words. Start a practice session to review all words.
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    ) : (
                        <Card className="mb-8">
                            <CardContent className="text-center py-12">
                                <h2 className="text-2xl font-bold mb-4">🎉 Great job!</h2>
                                <p className="text-xl text-gray-600 mb-6">
                                    You have no words to review right now.
                                </p>
                                <div className="space-x-4">
                                    <Button onClick={() => router.get(route('learning.index'))}>
                                        Continue Learning
                                    </Button>
                                    <Button 
                                        variant="outline" 
                                        onClick={() => router.get(route('test.index'))}
                                    >
                                        Take Daily Test
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    {/* Topic Statistics */}
                    {topicStats.length > 0 && (
                        <Card>
                            <CardHeader>
                                <CardTitle>Review Progress by Topic</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    {topicStats.map((stat, index) => (
                                        <div key={index} className="flex items-center justify-between p-3 border rounded">
                                            <div className="flex-1">
                                                <div className="flex items-center justify-between mb-2">
                                                    <span className="font-medium">{stat.topic}</span>
                                                    <span className="text-sm text-gray-600">
                                                        {stat.total_words} words
                                                    </span>
                                                </div>
                                                <div className="flex gap-6 text-sm text-gray-600">
                                                    <span>Avg mistakes: {stat.avg_mistakes?.toFixed(1)}</span>
                                                    <span>Avg correct: {stat.avg_correct?.toFixed(1)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}