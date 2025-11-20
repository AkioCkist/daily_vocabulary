# Dashboard Components Structure

## Overview
Cấu trúc component được tổ chức theo hướng modular, dễ maintain và scale cho production.

## Component Hierarchy

```
Home.vue (Main Page)
├── Header.vue (Global)
├── WelcomeSection.vue
├── GuestContent.vue (For non-authenticated users)
│   ├── WordFilter.vue
│   └── SubscribeForm.vue
└── Dashboard (For authenticated users)
    ├── StatsGrid.vue
    │   └── StatsCard.vue (×4)
    ├── LearningActivity.vue
    │   └── LearningHeatmap.vue
    ├── TrainingSection.vue
    ├── TopicsSection.vue
    ├── ReviewSection.vue
    └── RecentActivity.vue
```

## Component Details

### 1. **WelcomeSection.vue**
- **Purpose**: Display welcome message and icon
- **Props**: `user` (Object)
- **Features**: 
  - Dynamic greeting based on auth status
  - Clean, centered layout

### 2. **StatsCard.vue**
- **Purpose**: Reusable statistics card
- **Props**: 
  - `type` (String): 'learning' | 'accuracy' | 'streak' | 'mastered'
  - `value` (String/Number)
  - `label` (String)
  - `description` (String)
- **Features**: 
  - Dynamic icon and color based on type
  - Hover effects
  - Dark mode support

### 3. **StatsGrid.vue**
- **Purpose**: Container for 4 stat cards
- **Props**: `stats` (Object)
- **Composition**: Uses 4 StatsCard components

### 4. **LearningActivity.vue**
- **Purpose**: Display learning heatmap with period selector
- **Props**: 
  - `heatmapData` (Object)
  - `currentPeriod` (String)
- **Emits**: `changePeriod`
- **Child**: LearningHeatmap.vue

### 5. **TrainingSection.vue**
- **Purpose**: Training mode buttons
- **Emits**: 
  - `startQuick` - Start quick flashcards
  - `openAdvanced` - Open advanced modal

### 6. **TopicsSection.vue**
- **Purpose**: Display and manage topics
- **Props**: 
  - `topics` (Object)
  - `limit` (Number, default: 5)
- **Emits**: 
  - `select` - Select topic
  - `manage` - Open topic management

### 7. **ReviewSection.vue**
- **Purpose**: Show review due widget
- **Props**: `wordsCount` (Number)
- **Emits**: `start` - Start review session
- **Conditional**: Only shows if wordsCount > 0

### 8. **RecentActivity.vue**
- **Purpose**: Display recent learning activity
- **Props**: `activities` (Array)
- **Features**: 
  - Color-coded success/failure indicators
  - Responsive grid layout

### 9. **GuestContent.vue**
- **Purpose**: Content for non-authenticated users
- **Composition**: 
  - WordFilter component
  - SubscribeForm component

## Design Principles

### 1. **Single Responsibility**
Each component has one clear purpose and responsibility.

### 2. **Prop Drilling Minimization**
Data flows cleanly from parent to child without unnecessary intermediate layers.

### 3. **Event Bubbling**
Child components emit events that parent handles, keeping business logic at the top level.

### 4. **Reusability**
Components like StatsCard are designed to be reused with different props.

### 5. **Clean Code**
- Clear prop definitions with types and validation
- Descriptive variable and function names
- Minimal inline styles (uses Tailwind classes)
- Consistent code formatting

## Styling Approach

### Simplified UI
- Removed excessive animations and effects
- Clean borders and shadows
- Consistent spacing and padding
- Professional color scheme

### Responsive Design
- Mobile-first approach
- Grid layouts that adapt to screen size
- Proper breakpoints (md, lg)

### Dark Mode
- Full dark mode support across all components
- Consistent color variables

## State Management

State is managed at the Home.vue level:
- `heatmapPeriod` - Current heatmap view period
- `showFlashcardModal` - Flashcard modal visibility
- `showTopicModal` - Topic modal visibility

## API Integration

All API calls are handled in Home.vue using Inertia.js router:
- `startQuickFlashcards()` - POST /flashcards/start
- `startFlashcards(settings)` - POST /flashcards/start with custom settings
- `selectTopic(topic)` - POST /flashcards/start with topic
- `startReview()` - POST /flashcards/start for review
- `refreshDashboard()` - Reload dashboard data

## Future Enhancements

### Potential Improvements:
1. **Component Testing**: Add unit tests for each component
2. **Composables**: Extract shared logic into Vue composables
3. **Type Safety**: Add TypeScript for better type checking
4. **Loading States**: Add skeleton loaders for async data
5. **Error Boundaries**: Handle component errors gracefully
6. **Analytics**: Track user interactions per component
7. **Accessibility**: Enhance ARIA labels and keyboard navigation

## Maintenance Guidelines

### Adding New Components:
1. Create component in appropriate directory
2. Follow naming convention: PascalCase
3. Define clear props with types
4. Document with JSDoc comments
5. Emit events for parent communication
6. Keep styles within Tailwind classes

### Modifying Existing Components:
1. Check all parent usages first
2. Maintain backward compatibility
3. Update this documentation
4. Test in both light and dark modes
5. Verify responsive behavior

### Code Review Checklist:
- [ ] Component has single responsibility
- [ ] Props are properly typed
- [ ] Events are clearly named
- [ ] No hardcoded values (use props)
- [ ] Responsive design tested
- [ ] Dark mode tested
- [ ] No console.logs in production
- [ ] Follows existing code style
