# Changelog

All notable changes to Slick Forms will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-TBD

### Initial Public Release

This is the first public release of Slick Forms - a comprehensive Laravel package for building dynamic forms with an intuitive drag-and-drop interface.

#### Core Features

**Form Builder**
- Drag-and-drop interface with SortableJS integration
- 32 built-in field types covering all common use cases
- Schema-driven properties panel with automatic UI generation
- Real-time preview and validation
- Multi-page form support
- Form templates and duplication

**Field Types**
- **Input Fields**: Text, Textarea, Email, Number, Password, Phone, URL, Hidden
- **Selection Fields**: Select, Radio, Checkbox, Switch, Tags
- **Date/Time Fields**: Date, Time, Date Range
- **File Upload Fields**: File, Image, Video
- **Interactive Fields**: Star Rating, Slider, Range, Color Picker
- **Content Fields**: Header, Paragraph, Code
- **Advanced Fields**: Calculation, Repeater, Signature Pad, Location Picker, Rating Matrix, PDF Embed

**Layout System**
- Hierarchical layout with parent-child relationships
- Bootstrap 5 grid integration (Container, Row, Column)
- Component elements: Card, Accordion, Tabs, Carousel, Table
- Responsive column configuration with breakpoint support
- Nested layout structures for complex forms

**Conditional Logic**
- Field visibility conditions with multiple operators
- Conditional validation rules
- Visual field picker with eyedropper tool
- Support for complex logic combinations
- Dynamic value inputs based on target field type

**Form Management**
- Paginated form list with search and filtering
- Form status management (active/inactive)
- Form duplication and deletion
- Analytics dashboard with key metrics
- Template gallery with categorized templates

**Submissions & Data**
- Paginated submission viewer with search
- Individual submission details
- Export to CSV, Excel, and PDF
- IP tracking and timestamp logging
- Field-level data storage

**Analytics**
- Form views, starts, and completion tracking
- Conversion funnel visualization
- Field-level interaction metrics
- Device breakdown analytics
- Submissions over time charts
- Abandonment rate tracking

**Advanced Features**
- **URL Obfuscation**: Hashid-based secure URLs with optional signing
- **Webhooks**: POST submissions to external APIs with retry logic
- **Email Notifications**: Customizable email templates with variable substitution
- **Spam Protection**: Honeypot, rate limiting, CAPTCHA integration, IP blacklisting
- **Model Binding**: Populate forms from Eloquent models and save back to database
- **Dynamic Options**: Load select/radio options from URLs or Eloquent models
- **Form Versioning**: Automatic version snapshots with comparison and restoration
- **Success Screens**: Custom success messages, redirects, and downloads
- **QR Codes**: Generate scannable QR codes for form URLs
- **Input Masks**: Format input fields with masks for phone numbers, dates, etc.

**Developer Features**
- 19 singleton services for extensibility
- Event system with 15 events
- Queue jobs for async processing (email, webhooks, dynamic options)
- Comprehensive test suite with unit and feature tests
- Schema export for field types and layout elements
- Custom field type registration
- Custom layout element registration
- Middleware for form access control

**Documentation**
- Complete API reference for all services
- Field types reference with examples
- Layout elements reference
- Conditional logic guide
- Calculation fields guide
- Analytics guide
- Webhooks integration guide
- Email notifications guide
- Spam protection guide
- Model binding guide
- Form versioning guide
- Success screens guide
- QR codes guide
- Input masks guide
- Configuration reference
- Events and jobs reference
- Export functionality guide
- Custom field types tutorial
- Custom layout elements tutorial

**Technology Stack**
- Laravel 11-12
- Livewire 3
- PHP 8.2+
- Bootstrap 5
- SortableJS
- Alpine.js
- Tom Select (searchable selects)
- Flatpickr (date/time picker)
- Quill (WYSIWYG editor)
- Ace Editor (code editor)
- Signature Pad (signature capture)
- Leaflet (maps)
- Swiper (carousel)
- Hashids (URL obfuscation)

---

## Links

- **Repository**: [https://github.com/digitalisstudios/slick-forms](https://github.com/digitalisstudios/slick-forms)
- **Issues**: [https://github.com/digitalisstudios/slick-forms/issues](https://github.com/digitalisstudios/slick-forms/issues)
- **Packagist**: [https://packagist.org/packages/digitalisstudios/slick-forms](https://packagist.org/packages/digitalisstudios/slick-forms)
