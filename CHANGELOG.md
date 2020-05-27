# Release Notes for Back In Stock

## 1.4.0 - 2020-05-27
### Added
- Confirmation emails can be sent to the customer now (Feature Request #6).
- Confirmation emails can use a custom template, subject line and is optional (off by default).

### Fixed
- Fixed an issue where duplicate records could be added resulting in multiple emails being sent.
- Tested on Craft 3.4 and Commerce 3.1.

## 1.3.1 - 2020-04-08
### Fixed
- Response from form can now be translated (Thanks @pieter-janDB)

## 1.3.0 - 2019-12-20
### Added
- You can now automatically purge user details after emails are sent. Useful if GDPR or similar is a requirement.

## 1.2.0 - 2019-12-19
### Fixed
- Email notifications are only fired once. An issue could of occured previously if the product had multiple variants and the user selected anything but the first variant. Thanks @jmauzyk.

## 1.1.0 - 2019-06-09
### Added
- Ability to store additional options e.g. product attributes etc (Thanks Josh Crawford)
- Responses are now ajax friendly
- Added response message for when email is already detected
 
### Fixed
- Validation issues when no email was entered and other various responses
- Creating a new product would cause an error as no variant was found, this is now fixed 

## 1.0.1 - 2019-05-30
### Changed
- Minor update to plugin icon

## 1.0.0 - 2019-05-19
### Added
- Initial release
