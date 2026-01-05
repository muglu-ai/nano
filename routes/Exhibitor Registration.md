# Bengaluru Tech Summit 2026 – Exhibitor Registration Form

This document describes the **Exhibitor Registration Form** used for Bengaluru Tech Summit 2026.
It includes form sections, fields, dropdown values, validations, and implementation notes.

---

## 1. Overview

* **Form Type:** Exhibitor Registration
* **Flow:**

  1. Exhibitor Details
  2. Preview Details
  3. Submit Details
* **GST:** Applicable @ 18%
* **Booth Types:** Raw Space / Shell Space
* **CAPTCHA:** Image-based verification

---

## 2. Booth & Exhibition Details

### 2.1 Booth Space (Required)

Dropdown values:

* Raw
* Shell

**Notes:**

* Raw Space: Minimum 36 sqm
* Shell Space: Furnished booth with standard amenities

---

### 2.2 Booth Size (Required)

* Dropdown
* **Dynamically populated** based on Booth Space selection

---

### 2.3 Sector (Required)

Dropdown values:

* Information Technology
* Electronics & Semiconductor
* Drones & Robotics
* EV, Energy, Climate, Water, Soil, GSDI
* Telecommunications
* Cybersecurity
* Artificial Intelligence
* Cloud Services
* E-Commerce
* Automation
* AVGC
* Aerospace, Defence & Space Tech
* Mobility Tech
* Infrastructure
* Biotech
* Agritech
* Medtech
* Fintech
* Healthtech
* Edutech
* Startup
* Unicorn / VCs
* Academia & University
* Tech Parks / Co-Working Spaces of India
* Banking / Insurance
* R&D and Central Govt.
* Others

---

### 2.4 Subsector (Required)

Dropdown values:

* IT
* IoT
* AI & ML
* AR & VR
* BlockChain
* Digital Learning
* Electronics
* FinTech
* Robo & Drone
* Gaming
* Mobility
* IT Services
* BioTech
* AgriTech
* MedTech
* Healthtech
* SmartTech
* Cyber security & Human Resource
* EV
* Semiconductor
* Other

---

### 2.5 Other Sector Name

* Text input
* **Required only if Sector = “Others”**

---

### 2.6 Category (Required)

Dropdown values:

* Exhibitor
* Sponsor

---

## 3. Organisation & Address Details

* Name of Exhibitor (Organisation Name) *
* Invoice Address *
* City *
* State *
* Postal Code *

---

## 4. Contact Information

### 4.1 Organisation Contact

* Contact Number *
  Format: `CountryCode-AreaCode-Number` (Eg. `91-123412345`)
* Country Code Dropdown (Full international list)
* Website * (prefilled with `http://`)

---

### 4.2 Primary Contact Person

* Title * (Mr., Mrs., Ms., Dr., Prof.)
* First Name *
* Last Name *
* Designation *
* Email *
* Mobile * (Country code dropdown included)

---

## 5. Tax & Compliance Details

### 5.1 TAN

* Status Dropdown:

  * Registered
  * Unregistered (Not Available)
* TAN Number (Text input)

---

### 5.2 GST

* Status Dropdown:

  * Registered
  * Unregistered (Not Available)
* GST Number (Text input)

---

### 5.3 PAN

* PAN Number *

---

## 6. Sales Reference

* Sales Executive Name (From BTS Team) *

---

## 7. CAPTCHA & Validation

* CAPTCHA Image (server-generated)
* CAPTCHA Input Field *
* Hidden CAPTCHA Token

---

## 8. Hidden Fields (System)

* Nationality (default: Indian)
* Country code identifiers
* Payment method (Credit Card / Bank Transfer)
* CAPTCHA hash/token

---

## 9. Submission

* **Continue** button proceeds to Preview & Submission steps

---

## 10. Implementation Notes

* Booth Size dropdown depends on Booth Space (AJAX / dynamic)
* GST & TAN number fields are conditionally validated
* Country code dropdown reused for contact & mobile
* Payment details are informational (no user input)
* CAPTCHA validation is server-side

---

## 11. Recommendations

* Add client-side validation for GST/PAN formats
* Disable Booth Size until Booth Space is selected
* Auto-hide “Other Sector Name” unless needed
* Mask PAN/GST inputs on UI

---

## 12. Maintainer

**MM Activ Sci-Tech Communications Pvt. Ltd.**
Website: [https://bengalurutechsummit.com](https://bengalurutechsummit.com)

---

© Bengaluru Tech Summit 2026
