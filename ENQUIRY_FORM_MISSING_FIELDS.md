# Missing Fields in Enquiry Form

## Comparison with Reference Form: https://www.bengalurutechsummit.com/web/it_forms/enquiry.php

### ✅ Fields Currently Present:
1. Want Information About * (checkboxes)
2. Name * (with Title dropdown: Mr, Mrs, Ms, Dr, Prof)
3. Organisation *
4. Designation *
5. Email Address *
6. Contact Number *
7. Comment *
8. City *
9. Country *
10. How did you know about this event? *
11. reCAPTCHA (invisible - different from reference which uses image CAPTCHA)

---

### ❌ MISSING FIELDS:

#### 1. **Select Sector** * (REQUIRED - Major Missing Field)
   - **Type**: Dropdown
   - **Options from Reference**:
     - Information Technology
     - Electronics & Semiconductor
     - Drones & Robotics
     - EV, Energy, Climate, Water, Soil, GSDI
     - Telecommunications
     - Cybersecurity
     - Artificial Intelligence
     - Cloud Services
     - E-Commerce
     - Automation
     - AVGC
     - Aerospace, Defence & Space Tech
     - Mobility Tech
     - Infrastructure
     - Biotech
     - Agritech
     - Medtech
     - Fintech
     - Healthtech
     - Edutech
     - Startup
     - Unicorn/ VCs
     - Academia & University
     - Tech Parks / Co-Working Spaces of India
     - Banking / Insurance
     - R&D and Central Govt.
     - Others
   - **Database**: Already has `sector` field in `enquiries` table (nullable)
   - **Action Required**: Add dropdown field, fetch from `sectors` table or use hardcoded list

#### 2. **State** * (REQUIRED - Missing Field)
   - **Type**: Dropdown (should be dynamic based on Country selection)
   - **Current Status**: Field exists in database (`state` column) but not in form
   - **Action Required**: Add State dropdown that loads dynamically based on selected Country

---

### ⚠️ FIELDS WITH DIFFERENT OPTIONS:

#### 3. **Want Information About** - Missing Options:
   - **Current Options**: Attending as Delegate, Speaking Opportunity, Exhibiting, Sponsoring, B2B Meetings, Visitor, Conference & Awards, Other
   - **Reference Options**: Attending as Delegate, **Startup / POD**, Speaking Opportunity, Exhibition, Sponsoring, B2B Meetings, Visitor, **Poster**, Other
   - **Missing**: 
     - "Startup / POD" (should be added)
     - "Poster" (should be added)
   - **Note**: "Exhibition" vs "Exhibiting" - should match reference ("Exhibition")

#### 4. **How did you know about this event?** - Different Options:
   - **Current Options**: Website, Social Media, Email, Friend/Colleague, Advertisement, Other
   - **Reference Options**: 
     - Brochure
     - Colleague
     - Link on Site
     - Previous Attendee
     - Internet search
     - Sales Call
     - Association
     - Direct Mailer
     - News Paper Ad
     - Trade Publication
     - Invitation from Exhibitor
     - Hoarding
     - Others
   - **Action Required**: Update dropdown options to match reference exactly

---

### 📋 SUMMARY OF CHANGES NEEDED:

1. **Add "Select Sector" dropdown** (REQUIRED field) - before "Want Information About"
2. **Add "State" dropdown** (REQUIRED field) - between Country and City
3. **Update "Want Information About" options**:
   - Add "Startup / POD"
   - Add "Poster"
   - Change "Exhibiting" to "Exhibition"
4. **Update "How did you know about this event?" options** to match reference exactly
5. **Update validation** in `PublicEnquiryController.php` to include `sector` and `state` as required fields
6. **Update database save logic** to store `sector` and `state` values

---

### 🔧 Implementation Notes:

- The `enquiries` table already has `sector` and `state` columns (both nullable)
- Need to make `sector` required in validation
- Need to make `state` required in validation
- State dropdown should be dynamic (load states based on selected country)
- Can use existing `sectors` table or hardcode the sector list (similar to exhibitor registration)
- Can use existing `states` table for state dropdown (similar to other forms)

