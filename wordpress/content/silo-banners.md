# Silo Pages / Category Archive Banners

Each of the 5 silo categories gets a banner on its archive page automatically via the theme hook (`tw-silo-hero`), so no per-page work is required. To finish the look, paste the descriptions below into each category's **Description** field (Posts → Categories → edit each category).

The slugs are fixed and must match the theme and the n8n workflow exactly.

## 1. wellness-health

- Name: `Wellness & Health`
- Description:
  ```
  Supplements, checkups, and everyday wellness care that keeps your pet thriving — what works, what doesn't, and what the research actually says.
  ```

## 2. food-nutrition

- Name: `Food & Nutrition`
- Description:
  ```
  Fresh, balanced meals, treats, and feeding guidance for every stage of life — from puppy kibble to senior-appropriate nutrition plans.
  ```

## 3. gear-tech

- Name: `Gear & Tech`
- Description:
  ```
  Trackers, smart feeders, and practical gear for modern pet parenting — tested for real-world use, not just spec sheets.
  ```

## 4. training-behavior

- Name: `Training & Behavior`
- Description:
  ```
  Positive training methods and tools that build trust and a calmer home — from first cues to separation anxiety and reactivity.
  ```

## 5. senior-special-needs

- Name: `Senior & Special-Needs Pet Care`
- Description:
  ```
  Mobility aids, comfort measures, and tailored care for aging and special-needs pets — helping them stay comfortable and independent longer.
  ```

## Dedicated silo landing pages (optional)

The default category archives (styled by the theme header block + banner) satisfy the requirement. If you prefer dedicated static pages instead, create a page per category whose slug matches the category slug, embed the banner with an HTML block (example below), and link the primary menu to the page instead of the archive.

```html
<section class="tw-silo-hero">
  <span class="tw-icon-badge" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3C14.74 3 13.5 3.5 12 5c-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/></svg>
  </span>
  <div class="tw-silo-hero-copy">
    <h1 class="tw-silo-hero-title">Wellness &amp; Health</h1>
    <p class="tw-silo-hero-tagline">Supplements, checkups, and everyday care for a thriving pet.</p>
  </div>
</section>
```

Replace the SVG path with the icon for the relevant silo:

| Silo | Icon path (inside `<svg>`) |
|---|---|
| Wellness & Health (heart) | `<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3C14.74 3 13.5 3.5 12 5c-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/>` |
| Food & Nutrition (bowl) | `<path d="M4 14a8 8 0 0 1 16 0H4Z"/><path d="M5 14c0-4.5 2.67-7.5 7-7.5s7 3 7 7.5"/><path d="M12 6.5V4"/>` |
| Gear & Tech (tracker) | `<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>` |
| Training & Behavior (paw) | `<circle cx="6.5" cy="13.5" r="1.8"/><circle cx="12" cy="16.5" r="2.2"/><circle cx="17.5" cy="13.5" r="1.8"/><path d="M12 9.5C13.6 7.1 15.7 6 17.3 6c2 0 3.2 2 1.9 3.9"/><path d="M12 9.5C10.4 7.1 8.3 6 6.7 6c-2 0-3.2 2-1.9 3.9"/>` |
| Senior & Special-Needs (shield) | `<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1 1 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1Z"/><path d="m9 12 2 2 4-4"/>` |