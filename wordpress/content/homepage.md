# Homepage

Page slug: `home` (Path: `/`)
Set as static front page in **Settings → Reading → Your homepage displays → A static page**.

Structure (in order): header nav → hero → problem-first discovery (`#help`) → featured guides (`#guides`) → five silos → latest posts → trust & methodology (`#about`, `#method`) → newsletter → footer.

## How to build it (block editor)

1. **Hero** — one **HTML block**: paste the hero section below.
2. **Problem-first discovery** — one **HTML block**: paste the "What do you need help with?" section below.
3. **Featured guides** — one **HTML block**: paste the buying-guides section below.
4. **Silo heading** — one **HTML block**: paste the silo section head below (contains `id="silos"` for nav links).
5. **Silo grid** — a **Shortcode block**: `[tw_silo_grid]`
6. **Latest heading** — one **HTML block**: paste the latest section head below.
7. **Latest posts** — a **Shortcode block**: `[tw_latest_posts count="3"]`
8. **Trust & methodology** — one **HTML block**: paste the trust section below (contains `id="about"` and `id="method"`).
9. **Newsletter** — one **HTML block**: paste the newsletter section below (wire the form `action` to your ESP endpoint when chosen).

> Header/footer menus are theme-managed (Appearance → Menus) — not part of this page content.

## Hero (HTML block)

```html
<section class="tw-hero" id="top">
  <div class="tw-hero-text">
    <p class="tw-hero-kicker">For pet parents who want the best</p>
    <h1>Everything your pet will love, researched so you can shop with confidence</h1>
    <p class="tw-hero-lede">We check the supplements, foods, gear, and training tools we'd buy for our own pets — then share the honest picks, fast. No fluff, no guesses — and every affiliate link is clearly disclosed.</p>
    <p class="tw-hero-actions">
      <a class="tw-button tw-button-primary" href="/#help">Get help choosing</a>
      <a class="tw-button tw-button-ghost" href="/#guides">Browse guides</a>
    </p>
    <ul class="tw-trust-chips">
      <li>Independent research</li>
      <li>No sponsored rankings</li>
      <li>Evidence-aware content</li>
      <li>Transparent product selection</li>
    </ul>
  </div>
  <div class="tw-hero-visual" aria-hidden="true"></div>
</section>
```

The hero visual panel is styled by the theme (`style.css` provides the tinted surface); add brand illustration markup later if desired. Leave it empty for now.

## Problem-first discovery (HTML block)

Six intent cards. Each routes to the destination in the table below — **update the `href`s if a dedicated landing page ships later**.

```html
<section class="tw-home-section tw-help" id="help" aria-labelledby="tw-help-title">
  <div class="tw-section-head">
    <p class="tw-section-kicker">Start here</p>
    <h2 id="tw-help-title">What do you need help with?</h2>
    <p class="tw-section-lede">Tell us the problem — we'll point you to the right guide and the products that actually help.</p>
  </div>
  <div class="tw-intent-grid">
    <a class="tw-intent-card" href="/category/wellness-health/">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></span>
      <span class="tw-intent-body">
        <h3 class="tw-intent-title">Joint &amp; Mobility</h3>
        <p class="tw-intent-desc">Stiff joints, mobility support, and what the evidence actually says.</p>
      </span>
      <span class="tw-intent-arrow" aria-hidden="true">&rarr;</span>
    </a>
    <a class="tw-intent-card" href="/category/training-behavior/">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg></span>
      <span class="tw-intent-body">
        <h3 class="tw-intent-title">Anxiety &amp; Calming</h3>
        <p class="tw-intent-desc">Calming aids and routines for storms, travel, and alone time.</p>
      </span>
      <span class="tw-intent-arrow" aria-hidden="true">&rarr;</span>
    </a>
    <a class="tw-intent-card" href="/category/food-nutrition/">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M4 14a8 8 0 0 1 16 0H4Z"/><path d="M5 14c0-4.5 2.67-7.5 7-7.5s7 3 7 7.5"/><path d="M12 6.5V4"/></svg></span>
      <span class="tw-intent-body">
        <h3 class="tw-intent-title">Food &amp; Nutrition</h3>
        <p class="tw-intent-desc">Fresh food, kibble, treats, and feeding plans for every age.</p>
      </span>
      <span class="tw-intent-arrow" aria-hidden="true">&rarr;</span>
    </a>
    <a class="tw-intent-card" href="/category/gear-tech/">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span>
      <span class="tw-intent-body">
        <h3 class="tw-intent-title">GPS &amp; Safety</h3>
        <p class="tw-intent-desc">Trackers, fences, and gear that keep them findable.</p>
      </span>
      <span class="tw-intent-arrow" aria-hidden="true">&rarr;</span>
    </a>
    <a class="tw-intent-card" href="/category/senior-special-needs/">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M12 4v6"/><path d="M2 18h20"/></svg></span>
      <span class="tw-intent-body">
        <h3 class="tw-intent-title">Beds &amp; Comfort</h3>
        <p class="tw-intent-desc">Orthopedic beds and comfort picks for older or sensitive pets.</p>
      </span>
      <span class="tw-intent-arrow" aria-hidden="true">&rarr;</span>
    </a>
    <a class="tw-intent-card" href="/category/training-behavior/">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><circle cx="6.5" cy="13.5" r="1.8"/><circle cx="12" cy="16.5" r="2.2"/><circle cx="17.5" cy="13.5" r="1.8"/><path d="M12 9.5C13.6 7.1 15.7 6 17.3 6c2 0 3.2 2 1.9 3.9"/><path d="M12 9.5C10.4 7.1 8.3 6 6.7 6c-2 0-3.2 2-1.9 3.9"/></svg></span>
      <span class="tw-intent-body">
        <h3 class="tw-intent-title">Training &amp; Enrichment</h3>
        <p class="tw-intent-desc">Positive training tools and enrichment for a calmer home.</p>
      </span>
      <span class="tw-intent-arrow" aria-hidden="true">&rarr;</span>
    </a>
  </div>
</section>
```

**Intent → destination map** (temporary — replace with guide/article permalinks as they publish):

| Intent | Destination |
| --- | --- |
| Joint & Mobility | `/category/wellness-health/` |
| Anxiety & Calming | `/category/training-behavior/` |
| Food & Nutrition | `/category/food-nutrition/` |
| GPS & Safety | `/category/gear-tech/` |
| Beds & Comfort | `/category/senior-special-needs/` |
| Training & Enrichment | `/category/training-behavior/` (training-behavior is used twice — intentional until a dedicated training hub exists) |

## Featured guides (HTML block)

Cards currently deep-link to on-site search results for the guide topic — **swap each `href` to the article permalink once the guide is published** (keep the `/?s=` fallback until then).

```html
<section class="tw-home-section tw-guides" id="guides" aria-labelledby="tw-guides-title">
  <div class="tw-section-head">
    <p class="tw-section-kicker">Decision guides</p>
    <h2 id="tw-guides-title">Popular buying guides</h2>
    <p class="tw-section-lede">Short, evidence-aware guides that end in real picks — ranked by fit, never by who pays us.</p>
  </div>
  <div class="tw-guide-grid">
    <a class="tw-guide-card" href="/?s=joint+supplements">
      <span class="tw-guide-badge">Buying guide</span>
      <h3 class="tw-guide-title">Best Joint Supplements for Senior Dogs</h3>
      <p class="tw-guide-desc">What the evidence supports, what's just marketing, and the formulas we'd give our own aging dogs.</p>
      <span class="tw-guide-action">See our picks</span>
    </a>
    <a class="tw-guide-card" href="/?s=dog+gps+trackers">
      <span class="tw-guide-badge">Buying guide</span>
      <h3 class="tw-guide-title">Best Dog GPS Trackers</h3>
      <p class="tw-guide-desc">Battery life, range, and subscription costs compared — so you know what you're really paying for.</p>
      <span class="tw-guide-action">Compare options</span>
    </a>
    <a class="tw-guide-card" href="/?s=dog+beds+arthritis">
      <span class="tw-guide-badge">Buying guide</span>
      <h3 class="tw-guide-title">Best Dog Beds for Arthritis</h3>
      <p class="tw-guide-desc">Support foam, pressure relief, and washability — what actually helps stiff joints at night.</p>
      <span class="tw-guide-action">Find the right option</span>
    </a>
  </div>
</section>
```

## Silo section head (HTML block)

```html
<div class="tw-section-head" id="silos">
  <p class="tw-section-kicker">Browse by topic</p>
  <h2>All five hubs, one place</h2>
  <p class="tw-section-lede">Prefer to browse? Start from a topic hub — the same structure every guide is filed under.</p>
</div>
```

Then add the `[tw_silo_grid]` shortcode block (renders the five category cards linking to the real archives).

## Latest section head (HTML block)

```html
<div class="tw-section-head">
  <p class="tw-section-kicker">Fresh off the press</p>
  <h2>Latest from TailWell</h2>
</div>
```

Then add the `[tw_latest_posts count="3"]` shortcode block.

## Trust & methodology (HTML block)

`id="about"` is an in-page anchor fallback; the real About page lives at `/about/` — point menus there. `id="method"` is an in-page anchor for this section; the footer's "How we research" link points at `/review-methodology/` (the canonical methodology page).

```html
<section class="tw-home-section tw-method" id="about" aria-labelledby="tw-method-title">
  <div class="tw-section-head" id="method">
    <p class="tw-section-kicker">How we research</p>
    <h2 id="tw-method-title">Why you can trust TailWell</h2>
    <p class="tw-section-lede">We're an independent pet wellness resource run by pet parents. Every recommendation is checked against a written standard covering safety, evidence, price, and real-world usability — and products we can't verify never make it into our articles.</p>
  </div>
  <div class="tw-method-grid">
    <div class="tw-method-item">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></span>
      <h3 class="tw-method-title">Independent research</h3>
      <p class="tw-method-desc">Every guide starts with a fresh evidence pass — specs, label claims, and published studies, not press-release copy.</p>
    </div>
    <div class="tw-method-item">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg></span>
      <h3 class="tw-method-title">No sponsored rankings</h3>
      <p class="tw-method-desc">Companies can't pay for placement or position. If a product pays us, it still has to earn its spot.</p>
    </div>
    <div class="tw-method-item">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg></span>
      <h3 class="tw-method-title">Evidence-aware content</h3>
      <p class="tw-method-desc">We show where claims come from — anything we can't back up doesn't make the page.</p>
    </div>
    <div class="tw-method-item">
      <span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/></svg></span>
      <h3 class="tw-method-title">Transparent product selection</h3>
      <p class="tw-method-desc">We explain why each pick made the list — and every affiliate link is disclosed.</p>
    </div>
  </div>
  <p class="tw-method-more">Affiliate links never change what we recommend. <a href="/about/">More about TailWell</a></p>
</section>
```

## Newsletter (HTML block)

```html
<section class="tw-home-section" id="newsletter" aria-labelledby="tw-newsletter-title">
  <div class="tw-newsletter-band">
    <div>
      <h2 id="tw-newsletter-title">One honest guide, in your inbox.</h2>
      <p>No pop-ups, no spam. Short summaries of the guides we publish — unsubscribe anytime.</p>
    </div>
    <form class="tw-newsletter-form" action="#" method="post">
      <label class="tw-sr-only" for="tw-newsletter-email">Email address</label>
      <input type="email" id="tw-newsletter-email" name="email" placeholder="you@example.com" autocomplete="email" required>
      <button type="submit">Subscribe</button>
    </form>
  </div>
</section>
```

Wire `action` to your email service provider (ESP) endpoint when chosen; without it the form is presentational only.

## Destination summary (production)

| Homepage element | href |
| --- | --- |
| Header nav: Wellness / Food / Gear / Training / Senior Care | `/category/wellness-health/`, `/category/food-nutrition/`, `/category/gear-tech/`, `/category/training-behavior/`, `/category/senior-special-needs/` |
| Header nav: About | `/about/` (exists — see Step 6 of the runbook) |
| Hero primary CTA "Get help choosing" | `/#help` |
| Hero secondary CTA "Browse guides" | `/#guides` |
| Intent cards | see intent table above |
| Guide cards | `/?s=…` search fallbacks (swap to permalinks on publish) |
| Trust "More about TailWell" | `/about/` |
| Footer: About, How we research | `/about/`, `/review-methodology/` |
| Footer: Affiliate Disclosure, Privacy Policy, Cookie Policy, Terms of Use, Contact | `/affiliate-disclosure/`, `/privacy-policy/`, `/cookie-policy/`, `/terms-of-use/`, `/contact/` |
| Silo cards (shortcode) | the five category archives |

## Notes

- `[tw_latest_posts count="3"]` renders the 3 most recent published posts as cards with featured images (6 is the default — homepage uses 3 so the section stays secondary).
- `[tw_silo_grid]` renders the five locked category cards; do not change silo names/slugs.
- If you see `—` characters rather than the silo grid, the theme shortcodes are not active — confirm `tailwell-theme` is active.
- Newsletter has no ESP backend yet — subscribe is UI-only until an endpoint is chosen.
