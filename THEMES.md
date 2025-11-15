# Theme System Documentation

This application uses a flexible theme system built with Tailwind CSS v4 and CSS custom properties. The system supports multiple themes including light, dark, blue, green, enterprise, and system preference.

## How Themes Work

Themes are implemented using CSS custom properties (CSS variables) that define color values for different UI elements. Each theme class (`.light`, `.dark`, `.blue`, `.green`, `.enterprise`) contains a complete set of color variables.

## Current Themes

- **Light**: Default theme with light backgrounds
- **Dark**: Dark theme with dark backgrounds
- **Blue**: Blue-tinted theme with blue accents
- **Green**: Green-tinted theme with green accents
- **Enterprise**: Professional enterprise theme with slate grays and muted colors, now with a darker appearance for enhanced focus
- **System**: Automatically switches between light/dark based on system preference

## Adding New Themes

To add a new theme, follow these steps:

### 1. Update TypeScript Types

In `resources/js/composables/useAppearance.ts`, add your new theme to the `Appearance` type:

```typescript
type Appearance = 'light' | 'dark' | 'blue' | 'green' | 'purple' | 'system';
```

### 2. Update Theme Logic

In the same file, modify the `updateTheme` function to handle your new theme:

```typescript
} else if (value === 'purple') {
    document.documentElement.classList.add('purple');
}
```

### 3. Add CSS Variables

In `resources/css/app.css`, add a new CSS class with your theme's color variables:

```css
.purple {
    --background: hsl(270 40% 98%);
    --foreground: hsl(270 10% 4.9%);
    --primary: hsl(270 76.2% 36.3%);
    --primary-foreground: hsl(270 40% 98%);
    /* ... add all other color variables */
}
```

### 4. Update UI Component

In `resources/js/components/AppearanceTabs.vue`, add your new theme to the tabs array:

```typescript
{ value: 'purple', Icon: Palette, label: 'Purple' }
```

### 5. Update Server-Side Rendering

In `resources/views/app.blade.php`, add your theme to the class attribute and inline script:

```php
@class([
    'purple' => ($appearance ?? 'system') == 'purple'
])
```

And in the script:

```javascript
} else if (appearance === 'purple') {
    document.documentElement.classList.add('purple');
}
```

Also add a background color for your theme:

```css
html.purple {
    background-color: hsl(270 40% 98%);
}
```

## Color Variables Reference

Each theme should define these CSS custom properties:

- `--background`: Main background color
- `--foreground`: Main text color
- `--card`: Card background color
- `--card-foreground`: Card text color
- `--primary`: Primary brand color
- `--primary-foreground`: Text on primary color
- `--secondary`: Secondary color
- `--secondary-foreground`: Text on secondary color
- `--muted`: Muted background color
- `--muted-foreground`: Muted text color
- `--accent`: Accent color
- `--accent-foreground`: Text on accent color
- `--destructive`: Error/destructive color
- `--destructive-foreground`: Text on destructive color
- `--border`: Border color
- `--input`: Input field background
- `--ring`: Focus ring color
- `--chart-1` through `--chart-5`: Chart colors
- `--sidebar-*`: Sidebar-specific colors

## Best Practices

1. **Consistency**: Ensure all color variables are defined for each theme
2. **Accessibility**: Maintain good contrast ratios between foreground and background colors
3. **Testing**: Test themes across different components and pages
4. **Build**: Always run `npm run build` after making theme changes
5. **SSR**: Update both client-side and server-side theme handling

## Color Tools

- Use HSL color values for easy manipulation
- Tools like [Tailwind's color palette](https://tailwindcss.com/docs/customizing-colors) can help
- Consider using color generators for harmonious color schemes
