# Redis Cache Keys Documentation

## Complete Cache Key Reference

### 1. WordService Cache Keys
```
KEY: vocabulary:topics:list
TTL: 7 days
DATA: Collection of all topics
USAGE: Word filtering dropdown
INVALIDATION: Manual (static data)
LARAVEL KEY: laravel-database-laravel-cache-vocabulary:topics:list

KEY: vocabulary:cefr:levels
TTL: 7 days
DATA: Array of CEFR levels [A1, A2, B1, B2, C1, C2]
USAGE: CEFR level filtering
INVALIDATION: Manual (static data)
LARAVEL KEY: laravel-database-laravel-cache-vocabulary:cefr:levels
```

### 2. TopicService Cache Keys
```
KEY: topics:system:all
TTL: 7 days
DATA: System-wide predefined topics
USAGE: Topic selection UI
INVALIDATION: Manual (system topics rarely change)
LARAVEL KEY: laravel-database-laravel-cache-topics:system:all

KEY: topics:user:{user_id}
TTL: 1 day
DATA: User's custom and subscribed topics
USAGE: User dashboard, topic selection
INVALIDATION: On create/update/delete topic (TopicService methods)
LARAVEL KEY: laravel-database-laravel-cache-topics:user:123
EXAMPLE: laravel-database-laravel-cache-topics:user:1
         laravel-database-laravel-cache-topics:user:42
```

### 3. UserProgressService Cache Keys
```
KEY: user:progress:{user_id}
TTL: 6 hours
DATA: User statistics object
  {
    "total_words_seen": 150,
    "words_learning": 45,
    "words_learned": 85,
    "words_mastered": 20,
    "accuracy_rate": 78.5,
    "correct_answers": 523,
    "total_attempts": 665,
    "learning_streak": 12,
    "words_due_for_review": 30
  }
USAGE: Dashboard display, progress tracking
INVALIDATION: On updateWordProgress() call
LARAVEL KEY: laravel-database-laravel-cache-user:progress:123
EXAMPLE: laravel-database-laravel-cache-user:progress:1
         laravel-database-laravel-cache-user:progress:42
         laravel-database-laravel-cache-user:progress:999
```

### 4. DailyWordService Cache Keys
```
KEY: daily-word:{YYYY-MM-DD}
TTL: 24 hours (until midnight)
DATA: DailyWordHistory model with word relationship
USAGE: Word of the day feature
INVALIDATION: Automatic (24-hour TTL, changes at midnight)
LARAVEL KEY: laravel-database-laravel-cache-daily-word:2024-01-15
EXAMPLE: laravel-database-laravel-cache-daily-word:2024-01-15
         laravel-database-laravel-cache-daily-word:2024-01-16
```

### 5. ReviewService Cache Keys
```
KEY: review:progress:{user_id}
TTL: 1 hour
DATA: Review statistics object
  {
    "total_review_words": 45,
    "almost_mastered": 8,
    "struggling_words": 12,
    "recently_added": 5,
    "mastery_rate": 45.67
  }
USAGE: Review progress display
INVALIDATION: On submitReviewAnswer() call
LARAVEL KEY: laravel-database-laravel-cache-review:progress:123
EXAMPLE: laravel-database-laravel-cache-review:progress:1
         laravel-database-laravel-cache-review:progress:42
```

### 6. DashboardService Cache Keys
```
KEY: dashboard:data:{user_id}
TTL: 1 hour
DATA: Complete dashboard object
  {
    "stats": {...},
    "learning_heatmap": [...],
    "recent_activity": [...],
    "performance_trends": [...],
    "available_topics": [...],
    "cefr_levels": [...]
  }
USAGE: Dashboard page load
INVALIDATION: On submitAnswer() in TestService
LARAVEL KEY: laravel-database-laravel-cache-dashboard:data:123
EXAMPLE: laravel-database-laravel-cache-dashboard:data:1
         laravel-database-laravel-cache-dashboard:data:42

KEY: dashboard:stats:{user_id}
TTL: 1 hour
DATA: User statistics object (same as user:progress but cached separately)
USAGE: Dashboard stats section
INVALIDATION: On submitAnswer() in TestService
LARAVEL KEY: laravel-database-laravel-cache-dashboard:stats:123
EXAMPLE: laravel-database-laravel-cache-dashboard:stats:1
         laravel-database-laravel-cache-dashboard:stats:42
```

---

## Cache Key Patterns

### User-Specific Cache Keys
```
Pattern: {service}:{resource}:{user_id}
Examples:
  - user:progress:1
  - topics:user:42
  - review:progress:100
  - dashboard:data:5
  - dashboard:stats:5
```

### Date-Specific Cache Keys
```
Pattern: {service}-{resource}:{YYYY-MM-DD}
Examples:
  - daily-word:2024-01-15
  - daily-word:2024-01-16
```

### Global Cache Keys
```
Pattern: {service}:{resource}:{scope}
Examples:
  - vocabulary:topics:list
  - vocabulary:cefr:levels
  - topics:system:all
```

---

## Redis Commands to Check Cache

### List All Cache Keys
```bash
redis-cli KEYS "laravel-database-laravel-cache-*"
```

### View Specific Cache Value
```bash
redis-cli GET "laravel-database-laravel-cache-user:progress:1"
redis-cli GET "laravel-database-laravel-cache-vocabulary:topics:list"
redis-cli GET "laravel-database-laravel-cache-daily-word:2024-01-15"
```

### Count Cache Keys by Pattern
```bash
redis-cli KEYS "laravel-database-laravel-cache-user:progress:*" | wc -l
redis-cli KEYS "laravel-database-laravel-cache-dashboard:*" | wc -l
redis-cli KEYS "laravel-database-laravel-cache-topics:user:*" | wc -l
```

### Delete Specific Cache Key
```bash
redis-cli DEL "laravel-database-laravel-cache-user:progress:1"
redis-cli DEL "laravel-database-laravel-cache-dashboard:data:5"
```

### Delete All User Caches
```bash
redis-cli DEL "laravel-database-laravel-cache-user:progress:1" \
             "laravel-database-laravel-cache-dashboard:data:1" \
             "laravel-database-laravel-cache-dashboard:stats:1" \
             "laravel-database-laravel-cache-review:progress:1" \
             "laravel-database-laravel-cache-topics:user:1"
```

### Flush All Cache
```bash
redis-cli FLUSHDB
```

### Check Cache Memory
```bash
redis-cli INFO memory
```

### Get Key Expiration
```bash
redis-cli TTL "laravel-database-laravel-cache-user:progress:1"
# Returns: -1 (no expiration), -2 (doesn't exist), or seconds remaining
```

---

## Invalidation Triggers

### Cache Invalidation Chain

```
User adds word (UserVocabularyService::addWord)
  ├─ invalidates: user:progress:{user_id}

User removes word (UserVocabularyService::removeWord)
  ├─ invalidates: user:progress:{user_id}

User marks word learned (UserVocabularyService::markLearned)
  ├─ invalidates: user:progress:{user_id}

User submits test answer (TestService::submitAnswer)
  ├─ calls: UserProgressService::updateWordProgress()
  │   └─ invalidates: user:progress:{user_id}
  ├─ invalidates: dashboard:data:{user_id}
  ├─ invalidates: dashboard:stats:{user_id}
  └─ invalidates: review:progress:{user_id}

User submits review answer (ReviewService::submitReviewAnswer)
  ├─ calls: UserProgressService::updateWordProgress()
  │   └─ invalidates: user:progress:{user_id}
  └─ invalidates: review:progress:{user_id}

User creates topic (TopicService::createUserTopic)
  └─ invalidates: topics:user:{user_id}

User updates topic (TopicService::updateUserTopic)
  └─ invalidates: topics:user:{user_id}

User deletes topic (TopicService::deleteUserTopic)
  └─ invalidates: topics:user:{user_id}
```

---

## Cache Statistics

### Example Cache Usage for 1000 Active Users

| Cache Key Pattern | Entries | Memory per Entry | Total Memory |
|------------------|---------|-----------------|--------------|
| user:progress:* | 1000 | 2-3 KB | 2-3 MB |
| dashboard:data:* | 1000 | 5-8 KB | 5-8 MB |
| dashboard:stats:* | 1000 | 1-2 KB | 1-2 MB |
| review:progress:* | 500 | 1 KB | 500 KB |
| topics:user:* | 1000 | 2-5 KB | 2-5 MB |
| vocabulary:topics:list | 1 | 5-10 KB | 5-10 KB |
| vocabulary:cefr:levels | 1 | 1 KB | 1 KB |
| daily-word:* | 365 | 3-5 KB | 1-2 MB |
| **TOTAL** | **4,867** | **~3 KB avg** | **~13-20 MB** |

---

## Testing Cache Effectiveness

### Before Accessing Cache
```bash
# Watch Redis in real-time
redis-cli MONITOR

# In another terminal, load dashboard
# You should see cache keys being set in MONITOR output
```

### Verify Cache Hits
```bash
# Get initial stats
redis-cli INFO stats > stats_before.txt

# Use application
# (Load dashboard, answer questions, etc.)

# Get final stats
redis-cli INFO stats > stats_after.txt

# Compare keyspace_hits and keyspace_misses
# More hits = better cache efficiency
```

### Clear Cache for Testing
```php
// In code
Cache::flush(); // Clear everything

// Or specific keys
Cache::forget("user:progress:1");
Cache::forget("dashboard:data:1");
Cache::forget("review:progress:1");
```

---

## Related Files

- **Configuration**: `config/cache.php`
- **Implementation**: `app/Services/`
- **Documentation**: `REDIS_CACHING_IMPLEMENTATION.md`
- **Quick Reference**: `CACHING_QUICK_REFERENCE.md`
- **Utilities**: `unrestrict.php`, `diagnostic.php`
