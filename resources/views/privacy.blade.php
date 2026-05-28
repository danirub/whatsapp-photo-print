<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy — WhatsApp Photo Print</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f9fafb; color: #111827; line-height: 1.7; }
        .container { max-width: 760px; margin: 0 auto; padding: 48px 24px; }
        h1 { font-size: 32px; font-weight: 800; margin-bottom: 8px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 40px; }
        h2 { font-size: 18px; font-weight: 700; margin: 32px 0 10px; color: #1f2937; }
        p  { color: #374151; margin-bottom: 12px; }
        ul { color: #374151; padding-left: 20px; margin-bottom: 12px; }
        ul li { margin-bottom: 6px; }
        a  { color: #f59e0b; }
        .card { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
        footer { text-align: center; margin-top: 40px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Privacy Policy</h1>
        <p class="subtitle">Last updated: {{ date('F j, Y') }}</p>

        <h2>1. Introduction</h2>
        <p>
            Welcome to <strong>{{ config('app.name') }}</strong>. We operate a WhatsApp-based photo printing
            service ("Service"). This Privacy Policy explains how we collect, use, and protect your personal
            information when you use our Service.
        </p>

        <h2>2. Information We Collect</h2>
        <p>When you use our Service via WhatsApp, we may collect:</p>
        <ul>
            <li>Your <strong>WhatsApp phone number</strong></li>
            <li><strong>Photos</strong> you send to us for printing</li>
            <li><strong>Order details</strong> — print size, quantity, payment method</li>
            <li><strong>Payment information</strong> — processed securely via PayPlus; we do not store card details</li>
            <li><strong>Message content</strong> necessary to process your order</li>
        </ul>

        <h2>3. How We Use Your Information</h2>
        <ul>
            <li>To fulfill your photo printing orders</li>
            <li>To process payments</li>
            <li>To communicate with you about your order status</li>
            <li>To improve our Service</li>
        </ul>

        <h2>4. Data Retention</h2>
        <p>
            Your photos and order information are retained for up to <strong>90 days</strong> after your
            order is completed. You may request deletion of your data at any time by contacting us.
        </p>

        <h2>5. Data Sharing</h2>
        <p>We do not sell or share your personal data with third parties, except:</p>
        <ul>
            <li><strong>Meta (WhatsApp)</strong> — to deliver messages via WhatsApp Cloud API</li>
            <li><strong>PayPlus</strong> — to process credit card payments</li>
        </ul>

        <h2>6. Data Security</h2>
        <p>
            We implement appropriate technical and organizational measures to protect your personal information
            against unauthorized access, loss, or disclosure.
        </p>

        <h2>7. Your Rights</h2>
        <p>You have the right to:</p>
        <ul>
            <li>Request access to your personal data</li>
            <li>Request correction or deletion of your data</li>
            <li>Opt out of the Service at any time by sending "STOP" to our WhatsApp number</li>
        </ul>

        <h2>8. Children's Privacy</h2>
        <p>
            Our Service is not directed to children under 13. We do not knowingly collect personal
            information from children.
        </p>

        <h2>9. Changes to This Policy</h2>
        <p>
            We may update this Privacy Policy from time to time. We will notify you of significant changes
            via WhatsApp or by updating the date at the top of this page.
        </p>

        <h2>10. Contact Us</h2>
        <p>
            If you have questions about this Privacy Policy, contact us at:<br>
            <a href="mailto:{{ config('mail.from.address', 'info@' . parse_url(config('app.url'), PHP_URL_HOST)) }}">
                {{ config('mail.from.address', 'info@' . parse_url(config('app.url'), PHP_URL_HOST)) }}
            </a>
        </p>
    </div>

    <footer>
        &copy; {{ date('Y') }} {{ config('app.name') }} · <a href="{{ config('app.url') }}">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</a>
    </footer>
</div>
</body>
</html>
