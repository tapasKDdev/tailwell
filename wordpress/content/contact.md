# Contact

Page slug: `contact` (Path: `/contact/`)

Install **WPForms Lite** (lightweight, per project requirements) and create a simple contact form (Name, Email, Message). Paste the generated shortcode into a Shortcode block on this page — replace `[wpforms id="123"]` below.

## Page content

```html
<h2>Get in touch</h2>
<p>Questions about a recommendation, a correction, or a partnership? Send us a message — we reply to every inquiry.</p>
```

Then add a **Shortcode block**:

```
[wpforms id="123"]
```

## Notes

- Update the `id` to match the form WPForms actually generates.
- Optionally add a `_wp_http_referer`/honeypot — WPForms Lite includes spam protection enabled by default.
- Add the following as the page's SEO settings:
  - **Page title (Yoast):** `Contact — TailWell`
  - **Meta description (Yoast):** `Questions, corrections, or partnership inquiries? Reach the TailWell team. We reply to every message.`
  - **Focus keyword (Yoast):** `contact tailwell`