<x-filament-panels::page>
<style>
.stg-wrap { max-width: 680px; display: flex; flex-direction: column; gap: 20px; }
.stg-card { background: #1a1a2e; border: 1px solid rgba(255,255,255,.08); border-radius: 12px; overflow: hidden; }
.stg-card-header { padding: 14px 20px 12px; border-bottom: 1px solid rgba(255,255,255,.06); font-size: 13px; font-weight: 700; color: #f59e0b; display: flex; align-items: center; gap: 8px; }
.stg-card-body { padding: 18px 20px; display: flex; flex-direction: column; gap: 16px; }
.stg-field label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; margin-bottom: 6px; }
.stg-input { width: 100%; background: #0d0d1a; border: 1px solid rgba(255,255,255,.1); border-radius: 8px; padding: 9px 13px; font-size: 13px; color: #e5e7eb; outline: none; box-sizing: border-box; transition: border-color .15s; }
.stg-input:focus { border-color: #f59e0b; }
.stg-input[readonly] { color: #9ca3af; cursor: default; }
.stg-hint { font-size: 11px; color: #4b5563; margin-top: 5px; }
.stg-row { display: flex; gap: 8px; align-items: flex-end; }
.stg-row .stg-input { flex: 1; }
.stg-url-box { display: flex; align-items: center; gap: 8px; background: #0d0d1a; border: 1px solid rgba(245,158,11,.25); border-radius: 8px; padding: 9px 13px; }
.stg-url-text { flex: 1; font-size: 12px; color: #60a5fa; word-break: break-all; font-family: monospace; }
.btn-primary { background: #f59e0b; color: #000; border: none; border-radius: 8px; padding: 10px 22px; font-size: 13px; font-weight: 700; cursor: pointer; }
.btn-primary:hover { background: #d97706; }
.btn-secondary { background: transparent; border: 1px solid rgba(96,165,250,.4); color: #60a5fa; border-radius: 8px; padding: 9px 14px; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap; }
.btn-secondary:hover { background: rgba(96,165,250,.1); }
.btn-danger { background: transparent; border: 1px solid rgba(239,68,68,.4); color: #f87171; border-radius: 8px; padding: 9px 14px; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap; }
.btn-danger:hover { background: rgba(239,68,68,.1); }
.btn-copy { background: transparent; border: 1px solid rgba(255,255,255,.1); color: #9ca3af; border-radius: 6px; padding: 5px 10px; font-size: 11px; cursor: pointer; white-space: nowrap; flex-shrink: 0; }
.btn-copy:hover { border-color: rgba(255,255,255,.25); color: #e5e7eb; }
.badge-info { display: inline-block; background: rgba(96,165,250,.12); border: 1px solid rgba(96,165,250,.3); color: #60a5fa; border-radius: 6px; padding: 3px 10px; font-size: 11px; font-family: monospace; }
</style>

<div class="stg-wrap">

    {{-- General --}}
    <div class="stg-card">
        <div class="stg-card-header">⚙️ General</div>
        <div class="stg-card-body">
            <div class="stg-field">
                <label>Business / App Name</label>
                <input class="stg-input" type="text" wire:model="app_name" placeholder="WhatsApp Photo Print">
                <p class="stg-hint">Used in notifications and bot messages.</p>
            </div>
        </div>
    </div>

    {{-- Invite Link --}}
    <div class="stg-card">
        <div class="stg-card-header">🔗 Customer Invite Link</div>
        <div class="stg-card-body">
            <div class="stg-field">
                <label>Invite Secret</label>
                <div class="stg-row">
                    <input class="stg-input" type="text" wire:model="invite_secret" placeholder="ABC123-XYZ789">
                    <button class="btn-danger" wire:click="regenerateSecret" onclick="return confirm('Generate a new secret? Old invite links will stop working.')">🔄 Regenerate</button>
                </div>
                <p class="stg-hint">Included in the customer link. Regenerate to invalidate old links.</p>
            </div>

            <div class="stg-field">
                <label>WhatsApp Invite URL</label>
                <div class="stg-url-box">
                    <span class="stg-url-text" id="invite-url">{{ $this->getInviteUrl() }}</span>
                    <button class="btn-copy" onclick="copyToClipboard('invite-url', this)">📋 Copy</button>
                </div>
                <p class="stg-hint">Send this link to customers — clicking it opens WhatsApp with your number and a start message pre-filled.</p>
            </div>

            <div class="stg-field">
                <label>Quick WhatsApp Share</label>
                <a href="{{ $this->getInviteUrl() }}" target="_blank" class="btn-secondary" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                    💬 Open in WhatsApp
                </a>
            </div>
        </div>
    </div>

    {{-- Meta Webhook --}}
    <div class="stg-card">
        <div class="stg-card-header">🔌 Meta Webhook Configuration</div>
        <div class="stg-card-body">
            <div class="stg-field">
                <label>Webhook Callback URL</label>
                <div class="stg-url-box">
                    <span class="stg-url-text" id="webhook-url">{{ $this->getWebhookUrl() }}</span>
                    <button class="btn-copy" onclick="copyToClipboard('webhook-url', this)">📋 Copy</button>
                </div>
                <p class="stg-hint">Paste this in the Meta developer portal under WhatsApp → Webhooks.</p>
            </div>
            <div class="stg-field">
                <label>Verify Token</label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="badge-info">{{ $this->getVerifyToken() }}</span>
                    <span class="stg-hint" style="margin:0;">Set in <code style="color:#f59e0b;">.env</code> as <code style="color:#f59e0b;">WHATSAPP_VERIFY_TOKEN</code></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Save --}}
    <div>
        <button class="btn-primary" wire:click="save">💾 Save Settings</button>
    </div>

</div>

<script>
function copyToClipboard(elementId, btn) {
    const text = document.getElementById(elementId).textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = '✅ Copied!';
        setTimeout(() => btn.textContent = orig, 2000);
    });
}
</script>
</x-filament-panels::page>
