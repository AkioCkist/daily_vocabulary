import React, { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Progress } from '@/Components/ui/progress';

interface Word {
    id: number;
    word: string;
    definition: string;
    example: string;
    pronunciation?: string;
    topic: string;
    cefr_level: string;
}

interface LearningStats {
    total_words_learned: number;
    words_mastered: number;
    words_in_review: number;
    consecutive_correct_streak: number;
    total_mistakes: number;
}

interface Props {
    word: Word | null;
    filters: Record<string, any>;
    stats: LearningStats;
    user: any;
}

export default function LearningIndex({ word, filters, stats, user }: Props) {
    const [currentWord, setCurrentWord] = useState<Word | null>(word);
    const [loading, setLoading] = useState(false);
    const [excludeIds, setExcludeIds] = useState<number[]>([]);

    const getNextWord = async () => {
        setLoading(true);
        try {
            const response = await fetch(route('learning.next'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ exclude_ids: excludeIds }),
            });

            const data = await response.json();
            
            if (data.word) {
                setCurrentWord(data.word);
                if (currentWord) {
                    setExcludeIds(prev => [...prev, currentWord.id]);
                }
            } else {
                setCurrentWord(null);
            }
        } catch (error) {
            console.error('Error fetching next word:', error);
        } finally {
            setLoading(false);
        }
    };

    const markAsLearned = async () => {
        if (!currentWord) return;

        setLoading(true);
        try {
            const response = await fetch(route('learning.mark-learned'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ word_id: currentWord.id }),
            });

            const data = await response.json();
            
            if (data.success) {
                await getNextWord();
            }
        } catch (error) {
            console.error('Error marking word as learned:', error);
        } finally {
            setLoading(false);
        }
    };

    const addToReview = async () => {
        if (!currentWord) return;

        setLoading(true);
        try {
            const response = await fetch(route('learning.add-to-review'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ word_id: currentWord.id }),
            });

            const data = await response.json();
            
            if (data.success) {
                await getNextWord();
            }
        } catch (error) {
            console.error('Error adding to review:', error);
        } finally {
            setLoading(false);
        }
    };

    const backToFilters = () => {
        router.get(route('words.filter'));
    };

    return (
        <AuthenticatedLayout user={user}>
            <Head title="Learning Session" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Learning Stats */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">Words Learned</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.total_words_learned}</div>
                            </CardContent>
                        </Card>
                        
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">Mastered</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-green-600">{stats.words_mastered}</div>
                            </CardContent>
                        </Card>
                        
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">In Review</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-yellow-600">{stats.words_in_review}</div>
                            </CardContent>
                        </Card>
                        
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">Current Streak</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-blue-600">{stats.consecutive_correct_streak}</div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Word Card */}
                    {currentWord ? (
                        <Card className="mb-8">
                            <CardHeader>
                                <div className="flex justify-between items-start">
                                    <div>
                                        <CardTitle className="text-3xl font-bold mb-2">
                                            {currentWord.word}
                                        </CardTitle>
                                        <div className="flex gap-2 mb-4">
                                            <Badge variant="secondary">{currentWord.topic}</Badge>
                                            <Badge variant="outline">{currentWord.cefr_level}</Badge>
                                        </div>
                                    </div>
                                    {currentWord.pronunciation && (
                                        <Button 
                                            variant="outline" 
                                            size="sm"
                                            onClick={() => {
                                                // Add pronunciation audio functionality here
                                                console.log('Playing pronunciation for:', currentWord.word);
                                            }}
                                        >
                                            🔊 Pronunciation
                                        </Button>
                                    )}
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div>
                                        <h3 className="font-semibold text-lg mb-2">Definition</h3>
                                        <p className="text-gray-700">{currentWord.definition}</p>
                                    </div>
                                    
                                    {currentWord.example && (
                                        <div>
                                            <h3 className="font-semibold text-lg mb-2">Example</h3>
                                            <p className="text-gray-600 italic">"{currentWord.example}"</p>
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    ) : (
                        <Card className="mb-8">
                            <CardContent className="text-center py-12">
                                <p className="text-xl text-gray-600 mb-4">
                                    No more words available with current filters.
                                </p>
                                <Button onClick={backToFilters}>
                                    Back to Word Filter
                                </Button>
                            </CardContent>
                        </Card>
                    )}

                    {/* Action Buttons */}
                    {currentWord && (
                        <div className="flex flex-wrap gap-4 justify-center">
                            <Button 
                                onClick={getNextWord}
                                disabled={loading}
                                variant="outline"
                                size="lg"
                            >
                                {loading ? 'Loading...' : 'Next Random'}
                            </Button>
                            
                            <Button 
                                onClick={markAsLearned}
                                disabled={loading}
                                variant="default"
                                size="lg"
                                className="bg-green-600 hover:bg-green-700"
                            >
                                Mark Learned
                            </Button>
                            
                            <Button 
                                onClick={addToReview}
                                disabled={loading}
                                variant="default"
                                size="lg"
                                className="bg-yellow-600 hover:bg-yellow-700"
                            >
                                Add to Review
                            </Button>
                            
                            <Button 
                                onClick={backToFilters}
                                variant="outline"
                                size="lg"
                            >
                                Back to Filters
                            </Button>
                        </div>
                    )}

                    {/* Applied Filters */}
                    {Object.keys(filters).length > 0 && (
                        <Card className="mt-8">
                            <CardHeader>
                                <CardTitle className="text-lg">Applied Filters</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="flex flex-wrap gap-2">
                                    {Object.entries(filters).map(([key, value]) => (
                                        value && (
                                            <Badge key={key} variant="secondary">
                                                {key.replace('_', ' ')}: {value}
                                            </Badge>
                                        )
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