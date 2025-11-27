# 🔧 Technical Debt & Future Release Planning

## Story 1.1.1 - Event Landing Page: Future Improvements

### ✅ **COMPLETED (Current Release)**
- Event landing page frontend implementation with RDC support
- Backend models, jobs, and form requests
- Vue 3 compilation error fixes
- Complete registration flow functional
- Priority 1 blockers resolved and validated

---

## 📋 **LOGGED FOR FUTURE RELEASES**

### **Priority 2: Component Decomposition**
**Status:** Deferred to future release
**Current Issue:** EventLanding.vue (1333 lines) exceeds 300-line maintainability limit

#### **Planned Component Structure:**
```
resources/js/Components/Event/
├── EventLanding.vue (< 150 lines) - Main container
├── EventRegistrationForm.vue - Registration form logic
├── EventTestimonials.vue - Customer testimonials section
├── EventStats.vue - Trust indicators and statistics
└── AuthSuccessModal.vue - Post-registration auth flow
```

#### **Benefits Expected:**
- ✅ Improved maintainability (smaller, focused components)
- ✅ Better testability (individual component testing)
- ✅ Enhanced reusability (components usable elsewhere)
- ✅ Performance optimization (tree-shaking, lazy loading)

#### **Effort Estimation:** 2-3 developer hours

---

### **Priority 3: Analytics Integration**
**Status:** Future release
**Current State:** Google Analytics removed due to Vue 3 compilation restrictions

#### **Planned Implementation:**
```javascript
// Option A: Laravel layout level (app.blade.php)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>

// Option B: Vue 3 composable
// composables/useAnalytics.js
export function useAnalytics() {
  const trackEvent = (eventName, parameters) => {
    if (typeof gtag !== 'undefined') {
      gtag('event', eventName, parameters);
    }
  };
  return { trackEvent };
}
```

#### **Integration Points:**
- Event registration conversion tracking
- Form interaction analytics
- Modal engagement metrics
- Country-specific insights (especially RDC)

---

### **Priority 4: Email Templates**
**Status:** Future release
**Current State:** SendEventConfirmationEmail job logs only

#### **Planned Implementation:**
```bash
php artisan make:mail EventConfirmationMail
```

#### **Template Features:**
- Webinar confirmation details
- Calendar integration (.ics file)
- Event preparation materials
- Contact information for support
- Branding consistent with landing page

---

## 🎯 **ARCHITECTURE DECISIONS LOGGED**

### **Vue 3 Compliance**
- **Decision:** Remove `<script>` tags from Vue component templates
- **Rationale:** Vue 3 security compliance + CSP requirements
- **Future Work:** Implement analytics at application level

### **Form Request Pattern**
- **Decision:** Use EventRegistrationRequest instead of inline validation
- **Rationale:** Eliminate anti-patterns, improve maintainability
- **Status:** ✅ Implemented successfully

### **Async Job Processing**
- **Decision:** Use queued jobs for email confirmation
- **Rationale:** Improve form response time, handle email failures gracefully
- **Status:** ✅ Infrastructure ready, email templates pending

---

## 📈 **PERFORMANCE MONITORING**

### **Current Metrics Baseline:**
- Bundle size: 151.13 kB CSS + minimal JS
- Component count: 1 monolithic (EventLanding.vue)
- Build time: ~1.7s
- No compilation errors: ✅

### **Future Optimization Targets:**
- Component count: 5 modular components
- Individual component size: < 300 lines each
- Tree-shaking optimization potential
- Lazy loading implementation

---

## 🔄 **MIGRATION STRATEGY**

When implementing Priority 2 (Component Decomposition):

1. **Phase 1:** Extract AuthSuccessModal (lowest risk)
2. **Phase 2:** Extract EventStats and EventTestimonials
3. **Phase 3:** Extract EventRegistrationForm (highest complexity)
4. **Phase 4:** Refactor main EventLanding container
5. **Phase 5:** Add comprehensive component tests

**Risk Mitigation:** Implement one component at a time with regression testing

---

*Document created: 28 September 2025*
*Last updated: 28 September 2025*
*Next review: Before next sprint planning*