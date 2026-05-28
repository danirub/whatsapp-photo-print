<x-filament-panels::page>
<style>
/* ─── Layout shell ─── */
.cv-shell {
    display: flex;
    height: calc(100vh - 116px);
    min-height: 580px;
    overflow: hidden;
    /* Bleed edge-to-edge inside Filament's content area */
    margin: -1.5rem -1.5rem -1.5rem;
    border-top: 1px solid rgba(255,255,255,.06);
}

/* ─── Sidebar ─── */
.cv-sidebar {
    width: 300px;
    flex-shrink: 0;
    background: rgba(255,255,255,.025);
    border-right: 1px solid rgba(255,255,255,.07);
    display: flex;
    flex-direction: column;
}
.cv-sidebar-top {
    padding: 18px 16px 14px;
    background: rgba(255,255,255,.03);
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.cv-sidebar-title {
    font-size: 18px;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
    margin-bottom: 12px;
}
.cv-search {
    width: 100%;
    box-sizing: border-box;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 10px;
    padding: 8px 12px 8px 34px;
    font-size: 12px;
    color: #e5e7eb;
    outline: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: 10px center;
    background-size: 16px;
    transition: border-color .15s, background-color .15s;
}
.cv-search:focus { border-color: rgba(245,158,11,.4); background-color: rgba(255,255,255,.09); }
.cv-search::placeholder { color: #4b5563; }

.cv-list { flex: 1; overflow-y: auto; }
.cv-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid rgba(255,255,255,.03);
    transition: background .1s;
    position: relative;
}
.cv-item:hover { background: rgba(255,255,255,.04); }
.cv-item.active {
    background: rgba(245,158,11,.08);
    border-left: 3px solid #f59e0b;
}
.cv-item.active .cv-avatar { box-shadow: 0 0 0 2px #f59e0b; }

.cv-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 800;
    flex-shrink: 0;
    color: #fff;
}
.cv-avatar-text { font-size: 13px; font-weight: 800; }

.cv-item-body { flex: 1; min-width: 0; }
.cv-item-phone { font-size: 13px; font-weight: 700; color: #f3f4f6; }
.cv-item-preview { font-size: 11px; color: #6b7280; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cv-item-right { text-align: right; flex-shrink: 0; }
.cv-item-time { font-size: 10px; color: #4b5563; }
.cv-badge { display: inline-block; margin-top: 4px; background: #f59e0b; color: #000; border-radius: 10px; padding: 1px 7px; font-size: 10px; font-weight: 700; }
.cv-dir-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 4px; flex-shrink: 0; }
.cv-dir-dot.in  { background: #34d399; }
.cv-dir-dot.out { background: #f59e0b; }

.cv-empty-sidebar {
    padding: 40px 20px;
    text-align: center;
    color: #374151;
    font-size: 12px;
    line-height: 1.8;
}
.cv-empty-sidebar svg { opacity: .2; margin-bottom: 10px; }

/* ─── Main chat panel ─── */
.cv-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background-image:
        radial-gradient(circle at 20% 80%, rgba(245,158,11,.04) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(59,130,246,.04) 0%, transparent 50%),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='28'%3E%3Ccircle cx='1' cy='1' r='1' fill='rgba(255,255,255,.03)'/%3E%3C/svg%3E");
    min-width: 0;
}
.cv-chat-header {
    padding: 14px 20px;
    background: rgba(255,255,255,.03);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid rgba(255,255,255,.06);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}
.cv-header-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 800; color: #fff; flex-shrink: 0;
}
.cv-header-info { flex: 1; }
.cv-header-phone { font-size: 15px; font-weight: 700; color: #f9fafb; }
.cv-header-sub { font-size: 11px; color: #6b7280; margin-top: 1px; }
.cv-header-pill {
    font-size: 11px; font-weight: 600;
    border-radius: 20px; padding: 4px 12px;
    border: 1px solid; cursor: default;
}
.pill-amber { background: rgba(245,158,11,.12); border-color: rgba(245,158,11,.3); color: #fbbf24; }
.pill-green { background: rgba(52,211,153,.1); border-color: rgba(52,211,153,.25); color: #34d399; }
.pill-link {
    font-size: 11px; font-weight: 600;
    background: rgba(96,165,250,.1); border: 1px solid rgba(96,165,250,.3); color: #60a5fa;
    border-radius: 20px; padding: 4px 12px; text-decoration: none;
    transition: background .15s;
}
.pill-link:hover { background: rgba(96,165,250,.2); }

/* ─── Messages ─── */
.cv-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.cv-date-sep {
    display: flex; align-items: center; gap: 12px;
    margin: 10px 0; color: #374151; font-size: 11px;
}
.cv-date-sep::before, .cv-date-sep::after {
    content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.05);
}

.cv-msg { display: flex; align-items: flex-end; gap: 8px; }
.cv-msg.out { justify-content: flex-end; }
.cv-msg.in  { justify-content: flex-start; }
.cv-msg + .cv-msg.out,
.cv-msg + .cv-msg.in { margin-top: 2px; }

.cv-bubble-wrap { max-width: 62%; display: flex; flex-direction: column; }
.cv-msg.out .cv-bubble-wrap { align-items: flex-end; }
.cv-msg.in  .cv-bubble-wrap { align-items: flex-start; }

.cv-bubble {
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
    position: relative;
}
.cv-msg.out .cv-bubble {
    background: linear-gradient(135deg, #92400e, #78350f);
    color: #fef3c7;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.3);
}
.cv-msg.in .cv-bubble {
    background: #1e293b;
    border: 1px solid rgba(255,255,255,.07);
    color: #e2e8f0;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.25);
}
.cv-bubble-img {
    display: flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.07); border-radius: 10px;
    padding: 10px 12px; font-size: 12px; color: #94a3b8;
}
.cv-bubble-btns { font-size: 12px; }
.cv-btn-chip {
    display: inline-block; margin: 3px 3px 0 0;
    background: rgba(245,158,11,.15); border: 1px solid rgba(245,158,11,.3);
    color: #fbbf24; border-radius: 6px; padding: 3px 8px; font-size: 11px;
}
.cv-meta { font-size: 10px; color: #4b5563; margin-top: 4px; display: flex; align-items: center; gap: 5px; }
.cv-meta-label { font-size: 9px; background: rgba(255,255,255,.05); border-radius: 4px; padding: 1px 6px; color: #6b7280; }

/* ─── Reply bar ─── */
.cv-reply {
    flex-shrink: 0;
    padding: 12px 16px;
    background: rgba(255,255,255,.03);
    border-top: 1px solid rgba(255,255,255,.06);
    display: flex;
    gap: 10px;
    align-items: flex-end;
}
.cv-reply-input {
    flex: 1;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 22px;
    padding: 10px 16px;
    font-size: 13px;
    color: #e5e7eb;
    outline: none;
    resize: none;
    max-height: 120px;
    line-height: 1.5;
    transition: border-color .15s;
    font-family: inherit;
}
.cv-reply-input:focus { border-color: rgba(245,158,11,.5); }
.cv-reply-input::placeholder { color: #4b5563; }
.cv-reply-send {
    width: 42px; height: 42px;
    background: #f59e0b;
    border: none; border-radius: 50%;
    color: #000; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background .15s, transform .1s;
}
.cv-reply-send:hover { background: #d97706; transform: scale(1.05); }
.cv-reply-send:disabled { background: #374151; color: #6b7280; cursor: not-allowed; transform: none; }

/* ─── Image bubble ─── */
.cv-img-thumb {
    max-width: 220px;
    max-height: 220px;
    border-radius: 10px;
    display: block;
    cursor: zoom-in;
    border: 1px solid rgba(255,255,255,.1);
}
.cv-img-modal {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.85);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.cv-img-modal.open { display: flex; }
.cv-img-modal img { max-width: 90vw; max-height: 90vh; border-radius: 10px; }
.cv-img-modal-close {
    position: fixed; top: 20px; right: 24px;
    background: rgba(255,255,255,.1); border: none;
    color: #fff; font-size: 24px; cursor: pointer;
    border-radius: 50%; width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
}

/* ─── Empty state ─── */
.cv-empty-main {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 12px; color: #1f2937;
}
.cv-empty-main svg { opacity: .15; }
.cv-empty-main p { font-size: 13px; color: #374151; text-align: center; line-height: 1.7; }

/* ─── Right info panel ─── */
.cv-info {
    width: 230px; flex-shrink: 0;
    background: rgba(255,255,255,.025);
    border-left: 1px solid rgba(255,255,255,.07);
    display: flex; flex-direction: column;
    overflow: hidden;
}
.cv-info-top {
    padding: 16px;
    background: rgba(255,255,255,.03);
    border-bottom: 1px solid rgba(255,255,255,.06);
    font-size: 12px; font-weight: 700; color: #9ca3af;
    text-transform: uppercase; letter-spacing: .07em;
    display: flex; align-items: center; gap: 6px;
}
.cv-info-body { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 4px; }
.cv-info-card {
    background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.05);
    border-radius: 10px; padding: 12px; margin-bottom: 8px;
}
.cv-info-card h4 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #4b5563; margin: 0 0 8px; }
.cv-kv { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px solid rgba(255,255,255,.04); }
.cv-kv:last-child { border-bottom: none; }
.cv-kv label { font-size: 11px; color: #6b7280; }
.cv-kv .v { font-size: 12px; font-weight: 600; color: #e5e7eb; }
.cv-kv .v.amber { color: #fbbf24; }
.cv-kv .v.green { color: #4ade80; }
.cv-kv .v.red   { color: #f87171; }
.cv-status-pill {
    font-size: 11px; font-weight: 600; border-radius: 20px; padding: 3px 10px;
    text-transform: capitalize; display: inline-block;
}
.cv-empty-info { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center; }
.cv-empty-info svg { opacity: .15; margin-bottom: 8px; }
.cv-empty-info p { font-size: 11px; color: #374151; line-height: 1.7; }

/* Scrollbar */
::-webkit-scrollbar { width: 4px; height: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.15); }
</style>

@php
    $convList   = $this->getConversationList();
    $messages   = $this->getMessages();
    $orderInfo  = $this->getSelectedOrder();

    $avatarColors = ['#7c3aed','#2563eb','#059669','#d97706','#dc2626','#0891b2','#7c3aed','#db2777'];
    function cvAvatar(string $phone): string {
        $colors = ['#7c3aed','#2563eb','#059669','#d97706','#dc2626','#0891b2','#db2777'];
        $idx = crc32($phone) % count($colors);
        $col = $colors[abs($idx)];
        $initials = strtoupper(substr($phone, -2));
        return "<div class='cv-avatar' style='background:linear-gradient(135deg,{$col},rgba(0,0,0,.3))'>{$initials}</div>";
    }
@endphp

<div class="cv-shell">

    {{-- ── LEFT SIDEBAR ── --}}
    <div class="cv-sidebar">
        <div class="cv-sidebar-top">
            <div class="cv-sidebar-title">💬 Conversations</div>
            <input class="cv-search" type="text" placeholder="Search phone…"
                   oninput="filterConvs(this.value)">
        </div>

        <div class="cv-list" id="cv-list">
            @forelse($convList as $conv)
                @php
                    $colors = ['#7c3aed','#2563eb','#059669','#d97706','#dc2626','#0891b2','#db2777'];
                    $idx    = abs(crc32($conv['phone'])) % count($colors);
                    $color  = $colors[$idx];
                    $initials = strtoupper(substr($conv['phone'], -2));
                @endphp
                <div
                    class="cv-item {{ $this->selectedPhone === $conv['phone'] ? 'active' : '' }}"
                    wire:click="selectPhone('{{ $conv['phone'] }}')"
                    data-phone="{{ $conv['phone'] }}"
                >
                    <div class="cv-avatar" style="background: linear-gradient(135deg, {{ $color }}, rgba(0,0,0,.25))">
                        {{ $initials }}
                    </div>
                    <div class="cv-item-body">
                        <div class="cv-item-phone">+{{ $conv['phone'] }}</div>
                        <div class="cv-item-preview">
                            <span class="cv-dir-dot {{ $conv['last_dir'] === 'inbound' ? 'in' : 'out' }}"></span>
                            {{ $conv['last_msg'] }}
                        </div>
                    </div>
                    <div class="cv-item-right">
                        <div class="cv-item-time">{{ $conv['last_at'] }}</div>
                        <div class="cv-badge">{{ $conv['msg_count'] }}</div>
                    </div>
                </div>
            @empty
                <div class="cv-empty-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#6b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                    <p>No conversations yet.<br>Messages will appear<br>once customers message you.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── CENTER CHAT ── --}}
    <div class="cv-main">
        @if($this->selectedPhone)
            @php
                $colors2 = ['#7c3aed','#2563eb','#059669','#d97706','#dc2626','#0891b2','#db2777'];
                $idx2    = abs(crc32($this->selectedPhone)) % count($colors2);
                $headerColor = $colors2[$idx2];
                $headerInitials = strtoupper(substr($this->selectedPhone, -2));
            @endphp

            {{-- Header --}}
            <div class="cv-chat-header">
                <div class="cv-header-avatar" style="background: linear-gradient(135deg, {{ $headerColor }}, rgba(0,0,0,.3))">
                    {{ $headerInitials }}
                </div>
                <div class="cv-header-info">
                    <div class="cv-header-phone">+{{ $this->selectedPhone }}</div>
                    <div class="cv-header-sub">{{ count($messages) }} messages</div>
                </div>
                @if($orderInfo)
                    <span class="cv-header-pill pill-amber">Order #{{ $orderInfo['id'] }}</span>
                    <span class="cv-header-pill pill-green">{{ str_replace('_',' ',$orderInfo['status']) }}</span>
                    <a href="{{ route('filament.admin.resources.orders.view', $orderInfo['id']) }}"
                       class="pill-link">View Order →</a>
                @endif
            </div>

            {{-- Messages --}}
            <div class="cv-messages" id="cv-messages">
                @php $lastDate = null; @endphp

                @forelse($messages as $msg)
                    @if($lastDate !== $msg['date'])
                        <div class="cv-date-sep">{{ $msg['date'] }}</div>
                        @php $lastDate = $msg['date']; @endphp
                    @endif

                    <div class="cv-msg {{ $msg['direction'] === 'outbound' ? 'out' : 'in' }}">
                        <div class="cv-bubble-wrap">
                            <div class="cv-bubble" style="{{ $msg['type'] === 'image' && $msg['image_url'] ? 'padding:6px;' : '' }}">
                                @if($msg['type'] === 'image')
                                    @if($msg['image_url'])
                                        <img src="{{ $msg['image_url'] }}" class="cv-img-thumb"
                                             onclick="openImgModal('{{ $msg['image_url'] }}')">
                                    @else
                                        <div class="cv-bubble-img">
                                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                            Image received
                                        </div>
                                    @endif
                                @elseif($msg['type'] === 'buttons')
                                    @php
                                        $parts = explode("\n", $msg['content']);
                                        $body  = $parts[0] ?? '';
                                        $btns  = isset($parts[1]) ? str_replace('[Buttons: ', '', rtrim($parts[1], ']')) : '';
                                    @endphp
                                    <div style="margin-bottom:6px;">{{ $body }}</div>
                                    @foreach(explode(' | ', $btns) as $btn)
                                        <span class="cv-btn-chip">{{ trim($btn) }}</span>
                                    @endforeach
                                @else
                                    {{ $msg['content'] }}
                                @endif
                            </div>
                            <div class="cv-meta">
                                @if($msg['type'] !== 'text')
                                    <span class="cv-meta-label">{{ strtoupper($msg['type']) }}</span>
                                @endif
                                <span>{{ $msg['direction'] === 'outbound' ? '🤖' : '👤' }}</span>
                                <span>{{ $msg['time'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="cv-empty-main">
                        <svg width="64" height="64" fill="none" viewBox="0 0 24 24" stroke="#6b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                        <p>No messages logged yet<br>for this conversation</p>
                    </div>
                @endforelse
            </div>

            {{-- Reply Bar --}}
            <div class="cv-reply">
                <textarea
                    class="cv-reply-input"
                    wire:model="replyText"
                    placeholder="Type a message to send via WhatsApp…"
                    rows="1"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();@this.sendReply();}"
                ></textarea>
                <button class="cv-reply-send" wire:click="sendReply" wire:loading.attr="disabled">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                </button>
            </div>

        @else
            <div class="cv-empty-main">
                <svg width="72" height="72" fill="none" viewBox="0 0 24 24" stroke="#6b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                <p>Select a conversation<br>from the sidebar to view messages</p>
            </div>
        @endif
    </div>

    {{-- Image lightbox modal --}}
    <div class="cv-img-modal" id="cv-img-modal" onclick="closeImgModal()">
        <button class="cv-img-modal-close" onclick="closeImgModal()">✕</button>
        <img id="cv-img-modal-img" src="" alt="Order image">
    </div>

    {{-- ── RIGHT ORDER INFO ── --}}
    <div class="cv-info">
        <div class="cv-info-top">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
            Order Details
        </div>

        @if($orderInfo)
            <div class="cv-info-body">
                <div class="cv-info-card">
                    <h4>Order</h4>
                    <div class="cv-kv">
                        <label>ID</label>
                        <span class="v amber">#{{ $orderInfo['id'] }}</span>
                    </div>
                    <div class="cv-kv">
                        <label>Customer</label>
                        <span class="v" style="font-size:11px;">{{ $orderInfo['name'] }}</span>
                    </div>
                    <div class="cv-kv">
                        <label>Status</label>
                        <span class="v green" style="font-size:11px;">{{ str_replace('_',' ',$orderInfo['status']) }}</span>
                    </div>
                </div>

                <div class="cv-info-card">
                    <h4>Print Details</h4>
                    <div class="cv-kv">
                        <label>Size</label>
                        <span class="v">{{ $orderInfo['size'] }}</span>
                    </div>
                    <div class="cv-kv">
                        <label>Photos</label>
                        <span class="v amber">{{ $orderInfo['image_count'] }}</span>
                    </div>
                    <div class="cv-kv">
                        <label>Total</label>
                        <span class="v amber">₪{{ $orderInfo['total_price'] }}</span>
                    </div>
                </div>

                <div class="cv-info-card">
                    <h4>Payment</h4>
                    <div class="cv-kv">
                        <label>Status</label>
                        <span class="v {{ $orderInfo['payment'] === 'paid' ? 'green' : ($orderInfo['payment'] === 'failed' ? 'red' : '') }}">
                            {{ $orderInfo['payment'] ?? 'pending' }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('filament.admin.resources.orders.view', $orderInfo['id']) }}"
                   style="display:block; text-align:center; margin-top:4px; padding:9px; background:rgba(245,158,11,.12); border:1px solid rgba(245,158,11,.25); border-radius:10px; color:#fbbf24; font-size:12px; font-weight:700; text-decoration:none; transition:background .15s;"
                   onmouseover="this.style.background='rgba(245,158,11,.2)'"
                   onmouseout="this.style.background='rgba(245,158,11,.12)'">
                    📋 Open Full Order →
                </a>
            </div>
        @else
            <div class="cv-empty-info">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                <p>No order linked yet</p>
            </div>
        @endif
    </div>

</div>

<script>
// Auto-scroll to bottom
function scrollToBottom() {
    const el = document.getElementById('cv-messages');
    if (el) el.scrollTop = el.scrollHeight;
}
scrollToBottom();
document.addEventListener('livewire:updated', scrollToBottom);

// Image lightbox
function openImgModal(url) {
    document.getElementById('cv-img-modal-img').src = url;
    document.getElementById('cv-img-modal').classList.add('open');
}
function closeImgModal() {
    document.getElementById('cv-img-modal').classList.remove('open');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeImgModal(); });

// Sidebar search filter
function filterConvs(val) {
    const q = val.toLowerCase();
    document.querySelectorAll('#cv-list .cv-item').forEach(el => {
        const phone = el.dataset.phone || '';
        el.style.display = phone.includes(q) ? '' : 'none';
    });
}

// Auto-refresh every 8 seconds
setInterval(() => {
    if (typeof Livewire !== 'undefined') Livewire.dispatch('refresh');
}, 8000);
</script>
</x-filament-panels::page>
