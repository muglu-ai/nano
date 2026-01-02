Unified Event Ticketing & Association Platform
Project Revamp Spec (Config-First)
Version: v6 • Date: 2025-12-30

1. Goals and Scope
Build a modern, highly configurable ticketing system that can be launched fast, reused across events, and integrated into an existing Exhibitor/Sponsor portal without scaling issues in the portal users table.
1.1 Key Requirements (MVP)
Public ticket discovery (no login required).
Single-page registration (one page) with sections and dynamic delegate rendering.
Unlimited delegates under one company registration (no fixed cap like 1–7).
Configurable ticket catalog: category, subcategory, ticket type, pricing, capacity, sale windows.
Day-based access (e.g., VIP only Day 1) with admin-controlled rules.
Guest-first purchase flow (no login). Login is required only for upgrades / account features.
Association/sponsor quota-based free ticket distribution with sponsor dashboard + exports.
Admin-defined promo codes (rules + limits).
UTM/source tracking across registrations, orders, tickets.
Admin BI dashboard + CSV/Excel exports.
Bulk import for registrations and delegates (template-based).
Receipts: Provisional (optional) + Acknowledgment (paid/free).
Check-in & attendance is Phase 2 (not in MVP).
1.2 Non-Goals (Phase 2+)
Scanner / offline check-in app.
E-invoice integration (optional future).
Complex CRM automations or marketing journeys.
2. Actors and Access Model
To avoid scaling issues, attendee identities are stored outside the existing portal users table. Portal users continue to manage events, sponsors, reporting, and operations.
2.1 Roles
Portal Users (existing): super-admin, admin, sponsor, sales, ops/support.
Attendees (new domain): guest contacts and optional accounts.
2.2 Authentication Modes (Configurable per Event)
Policy
Registration
Purchase
Manage Booking
Upgrade
Guest Allowed (default)
No login; OTP optional
No login; OTP optional
Magic link + OTP fallback
Login required (OTP)
OTP Required (no account)
OTP required
OTP required
Magic link + OTP fallback
Login required (OTP)
Login Required
OTP login required
OTP login required
Logged-in portal
Logged-in portal

3. Configurable Event Setup
3.1 Event Branding and Meta
Event name, slug, dates, venue (reuse existing events).
Event logo and branding assets (new ticketing config fields).
Organizer details: organizer name, address, support phone/email, website.
Email routing: who receives copies for registrations/payments (accounts, sales, dev copy).
Email templates: registration success, payment success, sponsor-free confirmation, invite link usage, upgrade confirmation.
3.2 Ticketing Configuration (Admin)
Registration categories (separate from ticket types). Example: Delegate / Visitor / VIP / Student.
Ticket category -> subcategory -> ticket type hierarchy.
Pricing by ticket type and subcategory (member/non-member) and region/currency if needed.
Day access rules (ticket type entitlements and optional user selection if allowed).
Selection mode: same ticket for all delegates OR per-delegate selection.
Capacity & availability: total capacity, sold/reserved counters, sold-out badges.
Sale windows: sale start/end times per ticket type.
Promo codes: validity, rules, caps, applicable ticket sets, stack rules.
Associations: quotas, links, allowed ticket types, expiry, audit logging.
4. Registration UX (Single Page, Section-Based)
4.1 Single-Page Sections and Fields
A) Required Registration Information
Industry Sector (dropdown).
Organisation Type (dropdown).
Number of Delegate(s) (dropdown/stepper).
Association (hidden unless association context exists).
Nationality (radio).
Registration Category (dropdown; separate from ticket type).
Ticket Type (preselected from ticket page; changeable only if allowed).
Ticket Subcategory (member/non-member/student) if admin allows.
Day Access selection if admin allows; otherwise display read-only entitlements.
B) Organisation Information
Org Name, Country, State, City, Phone number (country code supported).
C) Billing (GST)
GST required: Yes/No.
If Yes: enter GSTIN -> fetch from GST API -> auto-fill legal name/address/state; lock fields unless admin unlocks.
D) Attendee Details (Dynamic by delegate count)
Salutation, First name, Last name.
Email, Phone.
Job title.
Optional: per-delegate ticket selection if selection mode allows.
4.2 Association Field Display Rule
Hide Association section by default.
If user arrives via association link: show association name as read-only.
If admin enabled manual association selection AND active associations exist: show dropdown.
All association attribution must be stored in ref_source_type/ref_source_id.
5. Commerce and Receipts
5.1 Payments
Online payments: UPI, Net Banking, Card (via gateway).
Webhook-based payment confirmation with idempotency.
Offline/On-spot: Admin Paid Invite Links (single-use), where admin chooses payment mode and amount before sharing link.
5.2 Receipts
Provisional receipt (optional) for unpaid states if enabled by event policy.
Acknowledgment receipt for paid / complimentary / sponsor-free / admin-invite paid.
Receipt numbering pattern configurable per event.
6. Sponsor / Association Quota System
Admin creates associations and allocates quotas per event and ticket type/subcategory.
System generates unique links for associations that auto-apply free allocation until quota exhausted.
Sponsor dashboard (portal users): used vs remaining, participant list, exports, UTM/source reporting.
Quota enforcement must be atomic (transaction-based) to prevent overuse.
7. Reporting & BI Dashboard (Admin)
Revenue: paid vs free vs sponsor-free, by event and date range.
Ticket performance: category/subcategory/type, sold-out tracking.
Source analytics: UTM breakdown + ref_source_type.
Promo performance: redemptions, discounts, caps.
Association utilization: allocation vs used, delegate lists.
Exports: CSV/Excel for finance and sponsor reporting.
8. Database Schema (Ticketing Domain)
Naming convention: ticket_* tables are new and scalable. Existing portal tables remain unchanged. The system reuses events.id and portal users.id only for admin/sponsor mappings.
8.1 Core Identity (Guest-first)
Table
Purpose
Key Fields
Keys / Notes
ticket_contacts
High-volume attendee identity (no account)
id, name, email, phone, verified flags
Unique email/phone (config); referenced by registrations/tickets.
ticket_accounts
Optional login layer (created on-demand)
id, contact_id, status, last_login_at
1:1 with contact. Used for upgrade/history.
ticket_otp_requests
OTP audit and throttling
contact_id, channel, otp_hash, expires_at, attempts, status
Rate-limit by contact + IP.
ticket_magic_links
Guest manage-booking access
contact_id, token, purpose, expires_at
Unique token; OTP fallback can reissue.

8.2 Event Configuration and Catalog
Table
Purpose
Key Fields
Keys / Notes
ticket_events_config
Event-level behavior switches
event_id, auth_policy, selection_mode, allow_subcategory, allow_day_select, email_cc_json
PK=event_id; FK to events.id.
event_days
Explicit event days
event_id, label, date
Unique(event_id,date).
ticket_registration_categories
Registration category (separate from ticket type)
event_id, name, is_active
Used for segmentation and rules.
ticket_categories
Ticket grouping
event_id, name, sort_order
Delegate/VIP/Workshop.
ticket_subcategories
Sub grouping
category_id, name
Member/Non-member/etc.
ticket_types
Sellable ticket types
event_id, category_id, subcategory_id?, name, price, capacity, sale windows
Entitlements and pricing live here.
ticket_type_day_access
Ticket type -> allowed days
ticket_type_id, event_day_id
PK(ticket_type_id,event_day_id).
ticket_inventory
Atomic stock control
ticket_type_id, reserved_qty, sold_qty
Use transactions to prevent oversell.
ticket_category_ticket_rules
Allowed combinations (category/subcategory/day)
registration_category_id, ticket_type_id, subcategory_id?, allowed_days_json
Validates user choices if selection is allowed.

8.3 Registration, Delegates, Tickets
Table
Purpose
Key Fields
Keys / Notes
ticket_registrations
Company header + attribution
event_id, contact_id, company fields, registration_category_id, utm_*, ref_source_type/id
Parent of delegates; stores source tracking.
ticket_delegates
Unlimited delegates
registration_id, name, email, phone, job_title
Unlimited rows; validate duplicates per registration.
ticket_delegate_assignments
Delegate -> ticket selection snapshot
delegate_id, ticket_type_id, subcategory_id?, day_access_snapshot_json, price_snapshot
Captures final entitlement used for issuance.
tickets
Issued tickets (one per delegate)
event_id, delegate_id, ticket_type_id, status, access_snapshot_json, source_type/id
QR optional in MVP; check-in in Phase 2.

8.4 Orders, Payments, Receipts
Table
Purpose
Key Fields
Keys / Notes
ticket_orders
Checkout order
registration_id, order_no, totals, status
One per registration; upgrades can create another order.
ticket_order_items
Order line items
order_id, ticket_type_id, qty, unit_price, total
Derived from delegates.
ticket_payments
Payments for orders
order_id, method, amount, status, gateway_txn_id, paid_at
Supports UPI/NB/Card/manual.
ticket_payment_events
Webhook/raw gateway logs
payment_id, event_type, payload_json
Audit and reconciliation.
ticket_receipts
Receipts
registration_id, order_id?, type, receipt_no, file_path, issued_at
Acknowledgment for paid/free/invite.

8.5 Associations, Promo, Invites, Imports, Upgrades
Table
Purpose
Key Fields
Keys / Notes
ticket_associations
Association/sponsor profile
name, logo_path, is_active
Linked to portal users via mapping table.
ticket_association_admins
Map portal users to associations
association_id, portal_user_id
FK to users.id (portal).
ticket_association_allocations
Quota allocations
association_id, event_id, ticket_type_id, allocated_qty, used_qty
Atomic increment of used_qty.
ticket_association_links
Shareable association links
allocation_id, token, expires_at
Unique token; sets ref_source_type.
ticket_promo_codes
Admin promo rules
event_id, code, type/value, valid_from/to, caps, rules_json
Unique(event_id,code).
ticket_promo_redemptions
Promo audit
promo_id, contact_id, order_id, discount_amount
Enforces per-contact and total caps.
ticket_admin_invites
Admin paid invite links
event_id, token, max_uses, allowed_ticket_ids_json, payment_method, paid_amount
FK created_by_portal_user_id to users.id.
ticket_bulk_import_jobs
Import jobs
event_id, uploaded_by_portal_user_id, file_path, status
Audit who imported and result.
ticket_bulk_import_rows
Import row errors (optional)
job_id, row_json, status, error_message
Helps debugging imports.
ticket_upgrades
Upgrade history
contact_id, old_ticket_id, new_ticket_id, upgrade_order_id
Requires login; preserves audit trail.

9. Integration Points with Existing Portal
Reuse events table as the master event registry (FK from ticketing tables).
Reuse portal users table ONLY for: admin/sponsor access, creating invites, BI dashboards, association admin mappings.
All attendee volume stays in ticket_contacts (and optional ticket_accounts).
Email CC routing stored per event in ticket_events_config (e.g., accounts/sales/dev copies).
10. Implementation Notes (MVP)
Prefer tokenized magic links for guest access; OTP fallback for security.
Enforce quotas and inventory with DB transactions/row locks to avoid overselling or overuse.
Store snapshots (price/day access) at assignment/ticket time to keep audits stable even if catalog changes later.
Keep check-in tables and scan logic deferred; still store access entitlements now.
