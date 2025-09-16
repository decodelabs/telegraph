# Changelog

All notable changes to this project will be documented in this file.<br>
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased
- Upgraded Stash to v0.8

---

### [v0.5.1](https://github.com/decodelabs/telegraph/commits/v0.5.1) - 10th September 2025

- Upgraded Kingdom to v0.2

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.5.0...v0.5.1)

---

### [v0.5.0](https://github.com/decodelabs/telegraph/commits/v0.5.0) - 21st August 2025

- Access Container from Kingdom instance
- Added Kingdom Service support
- Removed Veneer dependency
- Simplified Cache, Config and Store dependency injection

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.15...v0.5.0)

---

### [v0.4.15](https://github.com/decodelabs/telegraph/commits/v0.4.15) - 31st July 2025

- Added ConsentField support to ListInfo
- Added Consent helpers to Source and Context
- Improved group and tag helpers

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.14...v0.4.15)

---

### [v0.4.14](https://github.com/decodelabs/telegraph/commits/v0.4.14) - 16th July 2025

- Applied ECS formatting to all code

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.13...v0.4.14)

---

### [v0.4.13](https://github.com/decodelabs/telegraph/commits/v0.4.13) - 1st July 2025

- Fixed tag integer ID handling in MemberDataRequest

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.12...v0.4.13)

---

### [v0.4.12](https://github.com/decodelabs/telegraph/commits/v0.4.12) - 18th June 2025

- Improved order of operations for refresh methods

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.11...v0.4.12)

---

### [v0.4.11](https://github.com/decodelabs/telegraph/commits/v0.4.11) - 18th June 2025

- Only call update if user is already subscribed in updateUserAll

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.10...v0.4.11)

---

### [v0.4.10](https://github.com/decodelabs/telegraph/commits/v0.4.10) - 18th June 2025

- Call update if user is already subscribed in subscribeUser

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.9...v0.4.10)

---

### [v0.4.9](https://github.com/decodelabs/telegraph/commits/v0.4.9) - 18th June 2025

- Only return MemberInfo if subscribed by default
- Added force parameter to MemberInfo methods for unsubscribed states

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.8...v0.4.9)

---

### [v0.4.8](https://github.com/decodelabs/telegraph/commits/v0.4.8) - 18th June 2025

- Added helpers to check subscription status

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.7...v0.4.8)

---

### [v0.4.7](https://github.com/decodelabs/telegraph/commits/v0.4.7) - 18th June 2025

- Added refresh methods to context

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.6...v0.4.7)

---

### [v0.4.6](https://github.com/decodelabs/telegraph/commits/v0.4.6) - 18th June 2025

- Fixed group category name generation

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.5...v0.4.6)

---

### [v0.4.5](https://github.com/decodelabs/telegraph/commits/v0.4.5) - 18th June 2025

- Added group and tag info shortcuts

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.4...v0.4.5)

---

### [v0.4.4](https://github.com/decodelabs/telegraph/commits/v0.4.4) - 18th June 2025

- Added shortcuts to group and tag option list generators

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.3...v0.4.4)

---

### [v0.4.3](https://github.com/decodelabs/telegraph/commits/v0.4.3) - 17th June 2025

- Fixed 'categorized' typo in ListInfo

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.2...v0.4.3)

---

### [v0.4.2](https://github.com/decodelabs/telegraph/commits/v0.4.2) - 17th June 2025

- Fixed user and Disciple action interfaces

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.1...v0.4.2)

---

### [v0.4.1](https://github.com/decodelabs/telegraph/commits/v0.4.1) - 17th June 2025

- Fixed ListInfo handling in Adapters

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.4.0...v0.4.1)

---

### [v0.4.0](https://github.com/decodelabs/telegraph/commits/v0.4.0) - 17th June 2025

- Provide email separately for update actions
- Added AdapterActionResult structure
- Store action result MemberInfo where available
- Added Disciple actions

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.3.0...v0.4.0)

---

### [v0.3.0](https://github.com/decodelabs/telegraph/commits/v0.3.0) - 17th June 2025

- Renamed SubscriptionRequest to MemberDataRequest
- Simplified SubscriptionResponse interface
- Added FailureReason enum
- Added SourceReference structure
- Implemented Source controller interface
- Added shortcut methods to context
- Added PSR cache support
- Added data Store interface support
- Added Disciple support
- Implemented helper Commandment Actions

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.2.0...v0.3.0)

---

### [v0.2.0](https://github.com/decodelabs/telegraph/commits/v0.2.0) - 13th June 2025

- Setup Veneer frontage
- Build Config interface and Dovetail implementation
- Added initial Adapter interface structure
- Added initial Source container
- Implemented remote item models
- Added SubscriptionRequest structure

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.1.1...v0.2.0)

---

### [v0.1.1](https://github.com/decodelabs/telegraph/commits/v0.1.1) - 10th June 2025

- Added spread constructor to SubscriptionResponse

[Full list of changes](https://github.com/decodelabs/telegraph/compare/v0.1.0...v0.1.1)

---

### [v0.1.0](https://github.com/decodelabs/telegraph/commits/v0.1.0) - 10th June 2025

- Added initial boilerplate
- Ported SubscriptionResponse from r7
