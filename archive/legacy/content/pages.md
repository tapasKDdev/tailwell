# TailWell — Page Content (ready to paste into Gutenberg)

These are the real page body contents for each static page. Paste each into the
relevant WordPress page in the block editor, using the block patterns / shortcodes
noted. Menus, slugs, and pattern slots are referenced at the end.

All copy is brand-consistent: warm, credible, editorial. No marketing hype.

---

## 1. Homepage (front page, slug `/`)

Pattern: **TailWell: Homepage hero** → then **TailWell: 5 silo cards** (use
`[tw_silo_grid]`) → then a Manual "Latest articles" section.

**Body (after the hero pattern):**

> Heading block (H2): "Explore by topic"
> Shortcode block: `[tw_silo_grid]`

> Heading block (H2): "Latest articles"
> The grid is best built with a **Query Loop** block filtered to the 3 most recent
> posts (or paste `[tw_related_articles count="6"]` if you prefer the shortcode).

---

## 2. Silo pages (5)

For each, paste the matching **TailWell: {Silo} silo hub** pattern, which inserts
the silo banner, an intro paragraph, and an article list in one go. Or assemble
manually:

- **Wellness & Health** (`/wellness-health/`)
  Pattern `tailwell/silo-wellness` + `[tw_article_list count="6" category="wellness-health"]`
- **Food & Nutrition** (`/food-nutrition/`)
  Pattern `tailwell/silo-food` + `[tw_article_list count="6" category="food-nutrition"]`
- **Gear & Tech** (`/gear-tech/`)
  Pattern `tailwell/silo-gear` + `[tw_article_list count="6" category="gear-tech"]`
- **Training & Behavior** (`/training-behavior/`)
  Pattern `tailwell/silo-training` + `[tw_article_list count="6" category="training-behavior"]`
- **Senior & Special-Needs Care** (`/senior-special-needs/`)
  Pattern `tailwell/silo-senior` + `[tw_article_list count="6" category="senior-special-needs"]`

---

## 3. About page (`/about/`)

> Heading block (H1 not needed — page title renders it): "Who we are"

> Paragraph: "TailWell is a small, independent team of pet owners, writers, and
> researchers. We publish buying guides and honest reviews for pet families — with a
> particular focus on the senior and health-sensitive pets whose owners need genuinely
> reliable answers."

> Heading (H2): "Why we started TailWell"
> Paragraph: "Most pet-product content is written to sell a product, not answer a
> question. Rankings are bought, doses are hidden, and "studies show" is attached to
> things no study ever said. We started TailWell to publish the guide we wished existed
> when our own dogs got older: calm, generous, evidence-aware advice we could act on
> without feeling sold to."

> Heading (H2): "How we research"
> List (with icons):
> - We read the primary evidence first — published veterinary trials and ingredient factsheets.
> - We talk to veterinary nutritionists, behaviorists, and clinicians who work with senior pets.
> - We judge on label facts: printed doses, third-party testing, and cost per effective dose.
> - We disclose everything — when a link earns a commission, you'll know.
> - We update on a schedule; guides are re-reviewed at least twice a year.
>
> Paragraph: "No sponsored rankings. No paying to appear. No 'best of' lists built from
> a press release and a product photo."

> Heading (H2): "The pets we think about"
> Paragraph: "TailWell content is educational and should never replace a veterinarian's
> advice. When a decision matters for a health-sensitive pet, we'll say so — and we'll
> always recommend talking to your own vet before or while you shop."

> Button block: "Get in touch" → /contact/

---

## 4. Affiliate disclosure (`/affiliate-disclosure/`)

> Paragraph: "Some of the links on TailWell are affiliate links. This page explains in
> plain language what that means, what we earn, and — most importantly — what it never
> changes."

> Heading (H2): "How affiliate links work"
> Paragraph: "When you click a link on TailWell and buy a product from one of our retail
> partners, that partner may pay us a small commission. The price you pay is identical
> whether or not you use our link — shopping with us never costs you a cent more."

> Heading (H2): "What our commission never changes"
> List:
> - Rankings — products are chosen on dose transparency, testing, evidence, and value.
> - What we say — if a product we earn from has a weakness, we say so.
> - Our research process — it happens exactly the same with or without links.

> Heading (H2): "How TailWell is funded"
> Paragraph: "Affiliate commissions are our primary income, which is why we publish
> everything free to read. We treat that trust as the only reason the links exist."

> Heading (H2): "Contact"
> Paragraph: "Questions, corrections, or things that feel off? Reach us on the
> [contact page](/contact/)."

---

## 5. Privacy policy (`/privacy-policy/`)

> Heading (H2): "What we collect"
> Paragraph: "TailWell does not require an account and reading the site doesn't require
> you to share anything with us directly. In the background, like most websites, we use
> standard analytics for anonymized traffic stats. These tools may record things like
> browser type, referring page, and pages visited."

> Heading (H2): "Affiliate links & cookies"
> Paragraph: "Our affiliate partners may set cookies when you click their links so they
> can credit a referral. These cookies are controlled by the partner and governed by
> their privacy policies — not ours. We don't see, store, or receive your purchase data;
> we only receive a referral credit when a purchase is completed."

> Heading (H2): "Analytics"
> Paragraph: "We use a privacy-respecting analytics service to understand which guides
> readers find useful. This is aggregated and anonymized; it does not include personal
> identifiers like your name or email."

> Heading (H2): "Email contact"
> Paragraph: "If you email us through the contact page, we'll only use your address to
> reply. We don't add you to any mailing list without explicit consent, and we don't
> share addresses with anyone."

> Heading (H2): "Your rights"
> Paragraph: "Depending on where you live you may have rights to access, correct, or
> delete personal data we hold. For any privacy question, email hello@tailwell.com."

> Italic: "Last updated: March 2026."

---

## 6. Contact page (`/contact/`)

Use **TailWell: Contact** pattern, then replace the stub with your form plugin
(Contact Form 7, WPForms, etc.) shortcode or block. Suggested fields:
Name, Email, Topic (dropdown of the 5 silos + "Other"), Message.

---

## 7. 404 page

GeneratePress lets you create a custom 404 via the Customizer (Elements → 404).
Paste:

> Heading (H1): "This page has wandered off."
> Paragraph: "The link you followed is lost — a bit like a treat rolled under the sofa.
> The guides are still right where you left them."
> Buttons: "Back to the homepage" → / , and link the 5 silos below.

---

## Menu wiring (Appearance → Menus)

- **Primary menu** → assign to `primary`. Items (in order):
  Wellness & Health → /wellness-health/
  Food & Nutrition → /food-nutrition/
  Gear & Tech → /gear-tech/
  Training & Behavior → /training-behavior/
  Senior & Special-Needs → /senior-special-needs/
  About → /about/
- **Footer menu** → assign to `footer`. Items:
  About → /about/, Affiliate Disclosure → /affiliate-disclosure/,
  Privacy Policy → /privacy-policy/, Contact → /contact/
