# WP Updates Plugin

> Create polished update cards, organize them with categories, and place them anywhere on your WordPress site with one shortcode.

WP Updates Plugin gives you a simple home for product updates, announcements, release notes, news, and featured content. Build cards in WordPress admin, optionally add an image and link, then display a responsive card grid on any page.

## Highlights

- **Easy card management** — create, edit, preview, and delete update cards from WP Admin.
- **Category organization** — create reusable categories and assign them to one or more cards.
- **Flexible shortcode** — show all cards or filter a grid to selected categories.
- **Media Library support** — select a featured image directly from the WordPress Media Library.
- **Responsive layout** — use one, two, or three columns; the layout adapts for smaller screens.
- **Simple defaults** — choose a default label for card buttons in the Settings tab.

## Installation

1. Upload this plugin folder to `/wp-content/plugins/`, or install it from the WordPress Plugins screen.
2. Activate **WP Updates Plugin**.
3. In WordPress admin, open **WP Updates** from the left-hand menu.

## Admin guide

The **WP Updates** main page is split into four tabs:

| Tab | What it does |
| --- | --- |
| **All Cards** | Lists every update card. Select **Add Card** to create one, or edit/delete an existing card. |
| **Categories** | Add and manage category slugs, such as `product-updates` or `company-news`. |
| **Settings** | Set the default text used for a card button when that card has a link but no custom button label. |
| **How to Use** | Displays shortcut instructions and shortcode examples inside WordPress. |

### Create an update card

1. Go to **WP Updates → All Cards** and choose **Add Card**.
2. Enter a title and description.
3. Add one or more categories, separated by commas. You can create categories first from the **Categories** tab.
4. Optionally select an image, button link, and custom button text.
5. Select **Save Card**.

> Categories are saved as lowercase, URL-friendly slugs. For example, `Product Updates` becomes `product-updates`.

## Display cards on your site

Place the `[div_box]` shortcode in a page, post, block, or widget that supports shortcodes.

### Quick examples

```text
[div_box]
```

Displays all cards in a three-column grid.

```text
[div_box columns="2" limit="6"]
```

Displays up to six cards in two columns.

```text
[div_box categories="product-updates"]
```

Displays only cards assigned to `product-updates`.

```text
[div_box categories="product-updates, company-news" columns="3" show_description="false"]
```

Displays cards from either listed category, in three columns, without descriptions.

### Shortcode options

| Option | Default | Allowed values | Description |
| --- | --- | --- | --- |
| `columns` | `3` | `1`, `2`, `3` | Number of columns in the card grid. |
| `show_description` | `true` | `true`, `false` | Whether to show card descriptions. |
| `limit` | `0` | Any positive whole number | Maximum number of cards to display. `0` shows all cards. |
| `categories` | Empty | One or more category slugs | Comma-separated categories used to filter cards. |

## Category notes

- A card can belong to multiple categories.
- Category filtering matches cards in **any** supplied category.
- Removing a category in WP Admin also removes it from cards that use it. WordPress asks for confirmation before doing this.

## Requirements

- WordPress 5.0 or later
- PHP 7.4 or later

## Support

For help, begin with the in-plugin **How to Use** tab. Include your WordPress version, PHP version, and the shortcode you are using when reporting an issue.

## License

GPL v2 or later.
