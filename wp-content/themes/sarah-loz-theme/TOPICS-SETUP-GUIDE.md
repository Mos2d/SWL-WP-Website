# دليل إعداد نظام الموضوعات - Topics System Setup Guide

## نظرة عامة - Overview

تم إنشاء نظام موضوعات شامل يسمح للمستخدمين بتصفية المحتوى التعليمي حسب الموضوع. النظام يتضمن:

- **صفحة الموضوعات الرئيسية** - تعرض جميع الموضوعات المتاحة
- **أرشيف الموضوعات** - يعرض المحتوى المرتبط بموضوع معين
- **إدارة شاملة للموضوعات** - تخصيص المظهر والإعدادات
- **دعم لجميع أنواع المحتوى** - فيديوهات، أنشطة، ألعاب، تدريبات، مسرحيات

## الملفات المضافة - Files Added

### 1. ملفات القوالب - Template Files
- `page-topics.php` - صفحة الموضوعات الرئيسية
- `taxonomy-topic.php` - أرشيف الموضوعات الفردية

### 2. ملفات الوظائف - Function Files
- `inc/topic-admin.php` - إدارة الموضوعات
- `inc/taxonomies.php` - تحديث ليشمل تصنيف الموضوعات

### 3. ملفات المساعدة - Helper Files
- `create-sample-topics.php` - إنشاء موضوعات تجريبية
- `TOPICS-SETUP-GUIDE.md` - هذا الدليل

## خطوات الإعداد - Setup Steps

### الخطوة 1: إنشاء الموضوعات - Create Topics

1. **من لوحة التحكم** - Go to Admin Panel
   - انتقل إلى `أنشطة` → `إدارة الموضوعات`
   - أو `Activities` → `Topic Management`

2. **إنشاء موضوعات تجريبية** - Create Sample Topics
   - استخدم الملف `create-sample-topics.php` لإنشاء موضوعات تجريبية
   - أو أنشئ الموضوعات يدوياً من `المظهر` → `تصنيفات` → `الموضوعات`

### الخطوة 2: تخصيص الموضوعات - Customize Topics

لكل موضوع، يمكنك تخصيص:

- **صورة الموضوع** - Topic Image (300x300 بكسل مستحسن)
- **لون الموضوع** - Topic Color (يظهر في الواجهة)
- **وصف الموضوع** - Topic Description (يظهر في صفحة الموضوعات)
- **ترتيب العرض** - Display Order (الأقل = الأول)
- **موضوع مميز** - Featured Topic (يظهر في المقدمة)
- **أيقونة الموضوع** - Topic Icon (Dashicons)

### الخطوة 3: ربط المحتوى بالموضوعات - Link Content to Topics

1. **عند إنشاء/تعديل المحتوى** - When creating/editing content
   - اختر الموضوعات المناسبة من مربع "الموضوعات"
   - يمكن اختيار أكثر من موضوع واحد

2. **أنواع المحتوى المدعومة** - Supported Content Types
   - فيديوهات (Videos)
   - أنشطة (Activities)
   - ألعاب (Games)
   - تدريبات (Practices)
   - مسرحيات (Theaters)
   - بث مباشر (Broadcasts)
   - منتجات (Products)

## إعدادات النظام - System Settings

### إعدادات صفحة الموضوعات - Topics Page Settings

من `أنشطة` → `إدارة الموضوعات` → `الإعدادات العامة`:

- **عنوان صفحة الموضوعات** - Topics Page Title
- **وصف صفحة الموضوعات** - Topics Page Description
- **عدد الموضوعات في الصفحة** - Topics Per Page
- **عرض الموضوعات المميزة أولاً** - Show Featured Topics First

### إعدادات العرض - Display Settings

- **ترتيب الموضوعات** - Topics Order
- **الموضوعات المميزة** - Featured Topics
- **ألوان مخصصة** - Custom Colors
- **صور مخصصة** - Custom Images

## استخدام النظام - Using the System

### للمستخدمين - For Users

1. **تصفح الموضوعات** - Browse Topics
   - انتقل إلى صفحة "الموضوعات"
   - اختر الموضوع المطلوب

2. **تصفية المحتوى** - Filter Content
   - استخدم أزرار التصفية حسب نوع المحتوى
   - استعرض المحتوى المرتبط بالموضوع

### للمديرين - For Administrators

1. **إدارة الموضوعات** - Manage Topics
   - إضافة/تعديل/حذف الموضوعات
   - تخصيص المظهر والإعدادات
   - ترتيب عرض الموضوعات

2. **إدارة المحتوى** - Manage Content
   - ربط المحتوى بالموضوعات المناسبة
   - مراجعة المحتوى حسب الموضوع

## الميزات المتقدمة - Advanced Features

### نظام التصفية - Filtering System

- **تصفية حسب نوع المحتوى** - Filter by Content Type
- **تصفية حسب الموضوع** - Filter by Topic
- **بحث متقدم** - Advanced Search

### نظام الترتيب - Ordering System

- **ترتيب مخصص** - Custom Order
- **موضوعات مميزة** - Featured Topics
- **ترتيب تلقائي** - Automatic Ordering

### نظام التخصيص - Customization System

- **ألوان مخصصة** - Custom Colors
- **صور مخصصة** - Custom Images
- **أيقونات مخصصة** - Custom Icons
- **أوصاف مخصصة** - Custom Descriptions

## استكشاف الأخطاء - Troubleshooting

### مشاكل شائعة - Common Issues

1. **لا تظهر الموضوعات** - Topics Not Showing
   - تأكد من إنشاء الموضوعات
   - تحقق من إعدادات العرض
   - تأكد من ربط المحتوى بالموضوعات

2. **لا يعمل التصفية** - Filtering Not Working
   - تحقق من JavaScript
   - تأكد من تحديث الصفحة
   - تحقق من إعدادات القالب

3. **مشاكل في التصميم** - Design Issues
   - تحقق من CSS
   - تأكد من دعم المتصفح
   - تحقق من إعدادات الاستجابة

### حلول سريعة - Quick Fixes

1. **تحديث الروابط الثابتة** - Flush Permalinks
   - `إعدادات` → `روابط ثابتة` → `حفظ التغييرات`

2. **تحديث الكاش** - Clear Cache
   - إذا كنت تستخدم مكون إضافي للكاش

3. **إعادة تفعيل القالب** - Reactivate Theme
   - `المظهر` → `القوالب` → إعادة تفعيل

## التخصيص المتقدم - Advanced Customization

### إضافة حقول مخصصة - Adding Custom Fields

```php
// إضافة حقل مخصص للموضوع
add_action('topic_add_form_fields', 'add_custom_topic_field');
add_action('topic_edit_form_fields', 'add_custom_topic_field');

function add_custom_topic_field($term) {
    // كود الحقل المخصص
}
```

### تخصيص القوالب - Customizing Templates

1. **نسخ القوالب** - Copy Templates
   - انسخ `page-topics.php` إلى مجلد القالب الفرعي
   - عدل النسخة المنسوخة

2. **إضافة CSS مخصص** - Add Custom CSS
   - استخدم `style.css` في القالب الفرعي
   - أو استخدم مكون إضافي لإضافة CSS

### إضافة وظائف جديدة - Adding New Functions

```php
// دالة مخصصة لجلب الموضوعات
function get_custom_topics($args = array()) {
    return sarah_loz_get_ordered_topics($args);
}
```

## الدعم والصيانة - Support & Maintenance

### المهام الدورية - Regular Tasks

1. **مراجعة الموضوعات** - Review Topics
   - تأكد من صحة الروابط
   - تحقق من جودة الصور
   - مراجعة الأوصاف

2. **تنظيف المحتوى** - Clean Content
   - إزالة المحتوى القديم
   - تحديث الروابط
   - تحسين SEO

3. **نسخ احتياطي** - Backup
   - نسخ احتياطي للموضوعات
   - نسخ احتياطي للمحتوى
   - نسخ احتياطي للإعدادات

### التحديثات - Updates

1. **تحديث القالب** - Theme Updates
   - احتفظ بنسخة احتياطية
   - اختبر التحديثات
   - راجع التغييرات

2. **تحديث المكونات الإضافية** - Plugin Updates
   - تأكد من التوافق
   - اختبر الوظائف
   - راجع الإعدادات

## الخلاصة - Summary

نظام الموضوعات يوفر:

✅ **تنظيم شامل للمحتوى** - Comprehensive Content Organization  
✅ **تجربة مستخدم محسنة** - Enhanced User Experience  
✅ **إدارة سهلة للمديرين** - Easy Management for Administrators  
✅ **مرونة في التخصيص** - Flexibility in Customization  
✅ **دعم لجميع أنواع المحتوى** - Support for All Content Types  

للحصول على مساعدة إضافية، راجع:
- [WordPress Codex](https://codex.wordpress.org/)
- [WordPress Developer Resources](https://developer.wordpress.org/)
- [ACF Documentation](https://www.advancedcustomfields.com/resources/)

---

**ملاحظة**: هذا النظام مصمم خصيصاً لموقع سارة لوز التعليمي وقد يحتاج إلى تعديلات للاستخدام في مواقع أخرى.
