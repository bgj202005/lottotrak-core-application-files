# Terms of Service Agreement System for User Registration

This document outlines the implementation of a comprehensive terms of service agreement system that enhances the existing user registration process.

## Overview

The system requires new users to accept terms and conditions before completing their account registration. Users who decline the terms will not have an account created.

## Implementation Components

### 1. Database Changes

**Migration File**: `d:\wamp64\CI_application\application\migrations\20250926120000_add_terms_agreement_to_members.php`

Adds a new `terms_agreement` field to the members table:
- **Type**: BOOLEAN
- **Values**: 
  - `NULL` = User hasn't been asked yet
  - `TRUE` = User agreed to terms
  - `FALSE` = User declined terms
- **Default**: NULL

**To Apply Migration**:
```php
// Run the migration through CodeIgniter
// Navigate to: /admin/migrate or use CLI tools
```

### 2. Controller Changes

**File**: `d:\wamp64\CI_application\application\controllers\member.php`

**Modified Methods**:
- `register()` - Now redirects to terms agreement instead of immediately creating account
- `member_update()` - Added support for terms_agreement field

**New Methods**:
- `terms_agreement()` - Displays the terms and conditions page
- `process_terms()` - Handles user's agreement or decline response

### 3. Model Changes

**File**: `d:\wamp64\CI_application\application\models\member_m.php`

**Modified Methods**:
- `get_new_member()` - Added terms_agreement field initialization

### 4. View Files

**Terms Agreement Page**: `d:\wamp64\CI_application\application\views\templates\member\terms_agreement.php`
- Displays comprehensive terms of service
- Includes legal disclaimers and age requirements
- Provides "I Agree" and "I Decline" buttons

**Terms Declined Page**: `d:\wamp64\CI_application\application\views\templates\member\terms_declined.php`
- Shown when user declines terms
- Explains that registration was cancelled
- Provides options to return home or try registration again

### 5. JavaScript Enhancement

**File**: `d:\wamp64\www\lottotrak\js\registration-terms.js`
- Handles AJAX registration form submission
- Manages redirect to terms agreement page
- Provides proper error handling and user feedback
- Includes confirmation dialog for terms decline

## Registration Flow

### New User Registration Process:

1. **User fills out registration form** (username, email)
2. **System validates** username and email for uniqueness
3. **If validation passes**, user is redirected to terms agreement page
4. **User must choose**:
   - **"I Agree"**: Account is created, terms_agreement = TRUE, email validation sent
   - **"I Decline"**: No account created, user sees decline page
5. **If agreed**, normal email validation process continues

### URL Routes:

- `member/register` - Processes initial registration data
- `member/terms_agreement` - Displays terms and conditions
- `member/process_terms` - Handles agree/decline response
- `member/validate_email` - Continues to email validation (existing)

## Terms and Conditions Content

The terms include:

1. **Age Requirement**: Must be 18+ or legal age in jurisdiction
2. **Liability Disclaimer**: User accepts responsibility for losses
3. **Service Disclaimer**: No guarantees on prediction accuracy
4. **User Responsibilities**: Comply with local laws, use at own risk

## Security Features

### Uniqueness Validation:
- Username must be unique (5-15 characters)
- Email address must be unique and valid
- Proper form validation with CodeIgniter rules

### CSRF Protection:
- All forms include CSRF tokens
- JavaScript handles token management

### Session Security:
- Uses temporary session tokens for workflow
- Prevents unauthorized access to terms pages

## Database Schema

### Members Table Fields:
```sql
id              INT(11) AUTO_INCREMENT PRIMARY KEY
first_name      VARCHAR(100)
last_name       VARCHAR(100)  
email           VARCHAR(100)
password        VARCHAR(128)
username        VARCHAR(255)
reg_time        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
city            VARCHAR(100)
state_prov      VARCHAR(155)
country         INT(10)
lottery_id      INT
member_active   TINYINT(1) DEFAULT 0
subscription_key VARCHAR(255)
ip_address      BIGINT
terms_agreement BOOLEAN DEFAULT NULL  -- NEW FIELD
```

## Installation Instructions

### Step 1: Apply Database Migration
```bash
# Navigate to CodeIgniter application
cd /path/to/CI_application

# Run migration (if using CLI tools)
php index.php migrate
```

### Step 2: Update JavaScript References
Add to your main layout or registration pages:
```html
<script src="<?php echo base_url(); ?>js/registration-terms.js"></script>
```

### Step 3: Configure Base URLs
Ensure your views have proper base URL configuration:
```php
<script>
    var site_url = '<?php echo site_url(); ?>';
    var base_url = '<?php echo base_url(); ?>';
</script>
```

### Step 4: Test the Flow
1. Go to registration page
2. Enter unique username and email
3. Verify redirect to terms agreement
4. Test both "Accept" and "Decline" options
5. Confirm database entries are correct

## Customization Options

### Modify Terms Content:
Edit: `d:\wamp64\CI_application\application\views\templates\member\terms_agreement.php`

### Change Terms Logic:
Modify: `d:\wamp64\CI_application\application\controllers\member.php` - `process_terms()` method

### Update Styling:
Add custom CSS for terms pages in your stylesheet

## Troubleshooting

### Common Issues:

**1. Migration Fails**
- Check database permissions
- Verify migration file syntax
- Ensure no existing terms_agreement column

**2. JavaScript Errors**
- Verify jQuery is loaded
- Check console for errors
- Confirm base_url variables are set

**3. CSRF Token Issues**
- Ensure CSRF is enabled in CodeIgniter config
- Check token generation in forms
- Verify JavaScript token handling

**4. Redirect Problems**
- Check session management
- Verify flashdata is working
- Confirm route configuration

## Security Considerations

1. **Always validate input** on server-side regardless of client-side validation
2. **Use HTTPS** for all registration and terms pages
3. **Implement rate limiting** to prevent spam registrations
4. **Log terms acceptance** for legal compliance if required
5. **Regular security updates** for CodeIgniter framework

## Legal Compliance

### Recommendations:
- **Consult legal counsel** for terms content in your jurisdiction
- **Keep audit trail** of terms acceptance (already implemented in database)
- **Version control** terms content for future updates
- **Consider GDPR compliance** if serving EU users

## Support and Maintenance

### Regular Tasks:
- Monitor registration success/failure rates
- Review terms decline patterns
- Update legal content as needed
- Backup database with terms agreement records

### Monitoring Points:
- Check for high decline rates (may indicate issues with terms)
- Monitor for validation errors
- Track completion rates through full registration flow

---

## File Summary

**Database**: 
- `20250926120000_add_terms_agreement_to_members.php` - Migration

**Backend**:
- `member.php` - Controller updates
- `member_m.php` - Model updates  

**Frontend**:
- `terms_agreement.php` - Terms display page
- `terms_declined.php` - Decline confirmation page
- `registration-terms.js` - JavaScript handlers

**Documentation**:
- This README file

This system provides a complete, secure, and legally compliant terms agreement workflow for new user registrations.