# Age Selection System Setup Guide

## How to Set Up the Age Selection System

### 1. Create the Age Selection Page in WordPress Admin

1. Go to **WordPress Admin** → **Pages** → **Add New**
2. Set the page title as: `Age Selection` or `اختيار العمر`
3. Set the page slug as: `age-selection`
4. In the **Page Attributes** section, select **Template**: `Age Selection`
5. Leave the content area empty (the template will handle everything)
6. **Publish** the page

### 2. Keep Your Current Front Page Settings

- Do NOT set the age selection page as your front page
- Keep your current front page settings as they are
- The system will automatically redirect users to the age selection page when needed

### 3. How It Works

1. **First Visit**: When users visit your website for the first time, they'll be automatically redirected to the age selection page
2. **Age Selection**: Users choose their age group (3-5, 6-7, or 8-9 years)
3. **Personalized Content**: After selection, they're redirected back to the main site with personalized content
4. **Persistence**: The age selection is saved in both session and cookies for 30 days

### 4. Admin Features

- **Age Group Filtering**: All content (games, activities, videos) is automatically filtered based on selected age group
- **Personalized Messages**: Welcome messages and content are tailored to the selected age group
- **Easy Reset**: Users can change their age group anytime from the settings

### 5. Testing the System

1. **Clear your browser cookies** for your site
2. **Visit your website** - you should be redirected to the age selection page
3. **Select an age group** - you should be redirected back with personalized content
4. **Refresh the page** - you should stay on the main page (no redirect)

### 6. Debug Mode (For Admin Users Only)

If the system isn't working, you can use debug mode:

1. **Add `?debug_age_selection=1` to any URL on your site**
2. This will show a debug panel with:
   - Current age group status
   - Session and cookie values
   - Whether the age selection page is found
   - Buttons to clear age group and test

Example: `https://yoursite.com/?debug_age_selection=1`

### 7. Troubleshooting

**If you're not being redirected to age selection:**
- Clear your browser cookies completely
- Make sure you're not logged in as admin (admins might have different behavior)
- Check that the page slug is exactly `age-selection`
- Ensure the page template is set to "Age Selection"
- Verify the page is published (not draft)

**If the age selection page shows 404:**
- Make sure the page exists and is published
- Check the permalink structure in WordPress Settings → Permalinks
- Try flushing permalinks (go to Permalinks settings and click Save)

**If age selection doesn't persist:**
- Check if cookies are enabled in your browser
- Verify that your site allows session cookies
- Test in an incognito/private browser window

**If content isn't filtered after selection:**
- Use debug mode to verify the age group is set correctly
- Check that your posts/games/activities have the `age_range` custom field set
- Verify the age range values match exactly: `3-5`, `6-7`, or `8-9`

### 8. Important Notes

- **Never set the age selection page as your front page**
- The system uses a global redirect that works on all pages
- Age groups are: `3-5`, `6-7`, `8-9` (exact format required)
- Content filtering works automatically once age is selected

### 9. Customization

- Age group data is defined in `functions.php` in the `sarah_loz_get_age_groups()` function
- Colors and styling can be modified in both `page-age-selection.php` and `front-page.php`
- Content filtering logic is in the `sarah_loz_filter_content_by_age()` function

The system is now ready to use! 🎉

### 10. Step-by-Step Test Process

1. **Open an incognito/private browser window**
2. **Go to your website's homepage**
3. **You should be redirected to the age selection page**
4. **Select any age group**
5. **You should be redirected back to the homepage with personalized content**
6. **Refresh the page - you should NOT be redirected again**

If any step fails, use the debug mode to identify the issue! 