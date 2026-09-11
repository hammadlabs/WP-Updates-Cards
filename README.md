# Custom Div Box Manager WordPress Plugin

A powerful WordPress plugin that allows you to create, manage, and display custom div boxes with images, titles, and descriptions in a responsive grid layout similar to Google Web Stories.

## Features

### 🧩 Core Features
- **Admin Dashboard Interface**: Easy-to-use interface in wp-admin for managing div boxes
- **Image Management**: Upload images via WordPress Media Library
- **Content Fields**: Title (H2) and Description fields for each box
- **Live Preview**: Real-time preview as you type or select images
- **Responsive Grid**: Clean, responsive layout with hover effects
- **Shortcode & Widget**: Display boxes anywhere on your site

### 📱 Frontend Display Options
- **Shortcode**: `[div_box columns="3" show_description="true" limit="6"]`
- **Widget**: Available in Widgets admin area
- **Responsive**: Automatically adapts to different screen sizes
- **Latest First**: Newest boxes appear first by default

### 🎨 Design Features
- **Google Web Stories Style**: Clean, flat design similar to Google Web Stories
- **Hover Effects**: Subtle lift effect with shadow and translateY
- **Rounded Corners**: 14px border radius for modern look
- **Image Focus**: Image takes top 30% of card height
- **Theme Integration**: Matches WordPress theme background

## Installation

1. Upload the `custom-div-box-manager` folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Div Box Manager' in your WordPress admin menu

## Usage

### Admin Dashboard

1. **Navigate to Admin**: Go to `Div Box Manager` in your WordPress admin menu
2. **Add New Box**: Click "Add New" to create your first div box
3. **Fill Fields**:
   - **Image**: Click "Select Image" to choose from Media Library
   - **Title**: Enter your box title (will display as H2)
   - **Description**: Add your description content
4. **Live Preview**: See real-time preview as you type
5. **Save**: Click "Add Div Box" to save

### Frontend Display

#### Shortcode Usage
Place the shortcode anywhere in your posts, pages, or widgets:

```php
// Basic usage (3 columns, description shown)
[div_box]

// Custom columns (1, 2, or 3)
[div_box columns="2"]

// Hide description
[div_box show_description="false"]

// Limit number of boxes
[div_box limit="6"]

// Combined options
[div_box columns="2" show_description="true" limit="4"]
```

#### Widget Usage
1. Go to `Appearance > Widgets` in WordPress admin
2. Find "Div Box Manager" widget
3. Drag it to your desired widget area
4. Configure:
   - **Title**: Widget title (optional)
   - **Columns**: 1, 2, or 3 columns
   - **Show Description**: Toggle description display
   - **Limit**: Maximum number of boxes to show

## Admin Interface Features

### Managing Div Boxes
- **View All**: See all your div boxes in a clean list format
- **Edit**: Click "Edit" to modify any box in a modal popup
- **Delete**: Remove boxes with confirmation dialog
- **Live Preview**: Real-time preview during editing

### Image Management
- **Media Library Integration**: Full WordPress Media Library support
- **Image Preview**: See image thumbnails in admin
- **Remove Images**: Easy removal with dedicated button

## Customization

### CSS Classes
The plugin uses semantic CSS classes for easy customization:

```css
.cdbm-container          /* Main container */
.cdbm-box-card           /* Individual box */
.cdbm-box-image          /* Image section */
.cdbm-box-content        /* Content section */
.cdbm-box-title          /* H2 title */
.cdbm-box-description    /* Description text */
```

### Responsive Breakpoints
- **Desktop**: 3 columns (1200px+)
- **Tablet**: 2 columns (768px - 1199px)
- **Mobile**: 1 column (< 768px)

## Technical Details

### Database Storage
- Uses WordPress Options API for data storage
- Efficient sorting by creation date (newest first)
- Automatic data sanitization and validation

### Performance
- Lazy loading for images
- Optimized database queries
- Minimal CSS/JS footprint

### Browser Support
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Responsive design for all devices
- Accessibility features included

## Shortcode Parameters

| Parameter | Values | Default | Description |
|-----------|--------|---------|-------------|
| `columns` | 1, 2, 3 | 3 | Number of columns in grid |
| `show_description` | true, false | true | Show/hide descriptions |
| `limit` | number | unlimited | Maximum boxes to display |

## Hooks and Filters

The plugin is built with extensibility in mind. You can customize behavior using WordPress hooks:

```php
// Modify box data before display
add_filter('cdbm_box_data', 'my_custom_box_data');

// Customize container classes
add_filter('cdbm_container_classes', 'my_custom_classes');
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern browser with JavaScript enabled

## Support

For support and customization requests, please refer to the plugin documentation or contact the developer.

## Version History

- **1.0.0**: Initial release with core functionality
# WP-Updates-Cards
