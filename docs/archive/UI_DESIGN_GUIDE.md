# PetHelp - UI/UX Design Guide

## 🎨 Design Inspiration & Reference

### Primary Reference: [Rover.com](https://www.rover.com/)
**Why Rover?** Proven UX patterns for pet services platform with high user trust and engagement.

---

## 🎯 Design Principles

### 1. **Pet-Focused Design**
- Warm, friendly color palette (soft greens, blues, warm grays)
- Rounded corners and soft shadows
- Pet imagery as primary visual elements
- Trust indicators prominently displayed

### 2. **User Trust & Safety**
- Clear verification badges
- Prominent reviews and ratings
- Transparent pricing
- Easy-to-find safety information

### 3. **Mobile-First Approach**
- Touch-friendly buttons (min 44px)
- Readable typography on small screens
- Simplified navigation
- Thumb-friendly interactions

---

## 🎨 Color Palette (Based on Rover)

```css
/* Primary Colors */
--primary-green: #00A8A8;      /* Rover teal - CTA buttons, links */
--primary-dark: #007B7B;       /* Hover states */

/* Secondary Colors */
--warm-gray: #F7F7F7;          /* Background sections */
--light-gray: #E8E8E8;         /* Borders, dividers */
--dark-gray: #2D2D2D;          /* Primary text */
--medium-gray: #666666;        /* Secondary text */

/* Accent Colors */
--orange: #FF6B35;             /* Notifications, alerts */
--yellow: #FFD23F;             /* Ratings, highlights */
--red: #E74C3C;                /* Errors, cancellations */
--green: #27AE60;              /* Success states */

/* Background */
--bg-primary: #FFFFFF;         /* Main background */
--bg-secondary: #FAFAFA;       /* Card backgrounds */
```

---

## 📱 Component Design Patterns

### 1. **Sitter Cards (Rover Style)**
```vue
<!-- Key Elements -->
- Profile photo (circular, 80x80px)
- Name and title
- Star rating with count
- Services offered (badges)
- Starting price "from $X/night"
- "Book Now" CTA button
- Trust badges (verified, background check)
```

### 2. **Dashboard Layout**
```vue
<!-- Structure -->
- Header with user avatar and navigation
- Sidebar for main actions (mobile: bottom nav)
- Main content area with cards
- Quick actions floating button (mobile)
```

### 3. **Pet Profiles**
```vue
<!-- Pet Card Elements -->
- Large pet photo
- Pet name and basic info
- Personality traits (tags)
- Special needs indicators
- Quick edit button
```

---

## 🧩 Key Components To Create

### Priority 1: Cards & Lists
```bash
Components/Cards/
├── SitterCard.vue           # Individual sitter display
├── PetCard.vue             # Pet profile card
├── BookingCard.vue         # Booking status card
├── ServiceCard.vue         # Service type selection
└── ReviewCard.vue          # Review/rating display
```

### Priority 2: Forms & Inputs
```bash
Components/Forms/
├── PetForm.vue             # Add/edit pet
├── SitterProfileForm.vue   # Sitter onboarding
├── BookingRequestForm.vue  # Request booking
├── ReviewForm.vue          # Leave review
└── SearchFilters.vue       # Search parameters
```

### Priority 3: Navigation & Layout
```bash
Components/Layout/
├── DashboardHeader.vue     # User nav + actions
├── MobileBottomNav.vue     # Mobile navigation
├── SidebarNav.vue         # Desktop sidebar
└── BreadcrumbNav.vue      # Page navigation
```

---

## 📋 Page Layouts (Rover-Inspired)

### 1. **Owner Dashboard**
```
┌─────────────────────────────────────┐
│ Header (Welcome back, [Name])       │
├─────────────────────────────────────┤
│ Quick Actions Row                   │
│ [Find Sitter] [Add Pet] [Messages] │
├─────────────────────────────────────┤
│ My Pets Section                     │
│ [Pet Card] [Pet Card] [+ Add Pet]   │
├─────────────────────────────────────┤
│ Upcoming Bookings                   │
│ [Booking Card] [Booking Card]       │
├─────────────────────────────────────┤
│ Recent Activity Feed                │
└─────────────────────────────────────┘
```

### 2. **Sitter Dashboard**
```
┌─────────────────────────────────────┐
│ Header (Hello, [Name])              │
├─────────────────────────────────────┤
│ Profile Completion Bar              │
│ ████████████░░░ 75% Complete        │
├─────────────────────────────────────┤
│ New Requests (3)                    │
│ [Request Card] [Request Card]       │
├─────────────────────────────────────┤
│ Upcoming Services                   │
│ [Booking Card] [Booking Card]       │
├─────────────────────────────────────┤
│ Earnings This Week: $247            │
└─────────────────────────────────────┘
```

### 3. **Search Results Page**
```
┌─────────────────────────────────────┐
│ Search Bar + Filters                │
├─────────────────────────────────────┤
│ Map View Toggle                     │
├─────────────────────────────────────┤
│ Results Grid                        │
│ [Sitter] [Sitter] [Sitter]         │
│ [Card  ] [Card  ] [Card  ]          │
│                                     │
│ [Sitter] [Sitter] [Sitter]         │
│ [Card  ] [Card  ] [Card  ]          │
├─────────────────────────────────────┤
│ Load More / Pagination              │
└─────────────────────────────────────┘
```

---

## 🎨 Typography Scale

```css
/* Headings */
.text-h1 { font-size: 2.5rem; font-weight: 700; }     /* Page titles */
.text-h2 { font-size: 2rem; font-weight: 600; }       /* Section headers */
.text-h3 { font-size: 1.5rem; font-weight: 600; }     /* Card titles */
.text-h4 { font-size: 1.25rem; font-weight: 500; }    /* Subsections */

/* Body Text */
.text-lg { font-size: 1.125rem; line-height: 1.6; }   /* Large body */
.text-base { font-size: 1rem; line-height: 1.5; }     /* Default body */
.text-sm { font-size: 0.875rem; line-height: 1.4; }   /* Small text */
.text-xs { font-size: 0.75rem; line-height: 1.3; }    /* Captions */

/* Special */
.text-price { font-size: 1.25rem; font-weight: 700; color: var(--primary-green); }
.text-rating { font-size: 0.875rem; color: var(--yellow); }
```

---

## 🧭 Navigation Structure

### Main Navigation (Desktop)
```
Dashboard  |  Search  |  My Pets  |  Messages  |  Profile
```

### Mobile Bottom Navigation
```
[🏠 Home] [🔍 Search] [💬 Messages] [👤 Profile]
```

### User Account Menu
```
- Switch to Owner/Sitter
- Account Settings
- Payment Methods
- Help & Support
- Sign Out
```

---

## 📱 Responsive Breakpoints

```css
/* Mobile First Approach */
/* xs: 0px - 599px (default) */
/* sm: 600px+ */
@media (min-width: 600px) { }

/* md: 960px+ */
@media (min-width: 960px) { }

/* lg: 1264px+ */
@media (min-width: 1264px) { }

/* xl: 1904px+ */
@media (min-width: 1904px) { }
```

---

## 🔧 Implementation Notes

### TailwindCSS Custom Classes
```css
/* Create custom classes for common patterns */
.card-default {
  @apply bg-white rounded-lg shadow-sm border border-gray-200 p-6;
}

.btn-primary {
  @apply bg-primary-green text-white px-6 py-3 rounded-lg font-semibold 
         hover:bg-primary-dark transition-colors duration-200;
}

.btn-secondary {
  @apply bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold 
         hover:bg-gray-200 transition-colors duration-200;
}
```

### Icons & Assets
- **Icons:** Heroicons (already included with HeadlessUI)
- **Pet Images:** Placeholder service like picsum.photos or unsplash
- **User Avatars:** Generate initials or use gravatar
- **Illustrations:** Consider using undraw.co for empty states

---

## 🎯 Key UX Patterns from Rover

1. **Progressive Disclosure:** Don't overwhelm users with all options at once
2. **Social Proof:** Reviews and ratings prominently displayed
3. **Clear Pricing:** Always show "starting from" prices upfront
4. **Trust Signals:** Verification badges, background checks, insurance
5. **Mobile Optimization:** Key actions easily accessible on mobile
6. **Personalization:** Tailored content based on user type and history

---

**Next Step:** Start implementing the Owner Dashboard with these design patterns!