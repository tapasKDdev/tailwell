# Accessibility

Page slug: `accessibility` (Path: `/accessibility/`)

Paste the following as the page content. The statements below reflect what the theme actually implements (verified in the accessibility pass: skip link, keyboard navigation with focus return, visible focus rings, semantic landmarks, 44px tap targets). Do not add claims about formal audits that have not happened.

---

## Accessibility Statement

**Last updated:** September 25, 2026

TailWell wants as many people as possible to use this site — including people who use keyboards only, screen readers, magnification, or motion-reduction settings. This statement explains where we stand and how to tell us when something gets in your way.

### Our goal

We aim to meet **WCAG 2.1 Level AA** across the site. We have not yet commissioned a formal third-party accessibility audit; instead we build to the standard, test the parts we ship, and fix what is reported (see "Feedback" below).

### What we have built in

- **Keyboard first.** Everything clickable works with the keyboard. The mobile menu opens and closes with Enter/Space/Escape, and focus returns to the menu button when it closes.
- **Skip link.** The first Tab on any page reaches a "Skip to content" link that jumps past the navigation.
- **Visible focus.** Focused elements show a clear focus outline — focus is never hidden.
- **Semantic structure.** Pages use real landmarks (`header`, `nav`, `main`, `footer`), a single page-level `h1`, and ordered headings underneath.
- **Screen reader labels.** Controls have accessible names (menu toggle, form fields, icon-only elements are either labeled or marked decorative with `aria-hidden`).
- **Motion.** Respects `prefers-reduced-motion` where motion is scripted; nothing essential moves to be understood.
- **Targets and spacing.** Interactive controls are sized for touch (at least 44×44 CSS pixels where feasible) with spacing that limits mis-taps.
- **No horizontal scrolling.** Pages are designed to reflow without sideways scrolling down to 320px-wide screens.
- **Color.** Text/background pairings are chosen for readable contrast, and color is never the only signal for meaning (badges and labels carry text).
- **Forms.** Fields have programmatic labels, and errors are described in text, not only by color.

### Known limitations

- **Third-party content.** Product images and pages on retailer sites (Amazon, Chewy, and others) follow their own accessibility standards — we cannot fix those from here.
- **Older archive pages.** Some archived template pages may not yet match every improvement made to newer templates; these are being brought up to standard over time.
- **PDFs.** Any document we publish as a PDF may not yet fully conform; ask us via [Contact](/contact/) and we will provide the information in another format.

### Feedback

Something on TailWell blocking you? Tell us via the [Contact](/contact/) page:

- the page URL,
- what you were trying to do, and
- what happened (browser and assistive technology, if you know them).

We aim to acknowledge accessibility reports within 7 days and to fix confirmed issues as quickly as we can, prioritizing problems that block core reading and navigation.

### Formal compliance note

WCAG 2.1 AA is our working target. No claim of formal certification or audit is made on this page, because none has been performed — if that changes, this statement will be updated.

---

## SEO setup

- **Page title (Yoast):** `Accessibility Statement — TailWell`
- **Meta description (Yoast):** `TailWell targets WCAG 2.1 AA: keyboard navigation, skip links, visible focus, screen-reader labels, reduced motion — and a route to report barriers.`
- **Focus keyword (Yoast):** `accessibility statement`
