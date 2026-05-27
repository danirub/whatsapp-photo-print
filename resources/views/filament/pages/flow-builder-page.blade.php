<x-filament-panels::page>

    {{-- Drawflow CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/drawflow@0.0.59/dist/drawflow.min.css">

    <style>
        .flow-wrap {
            display: flex;
            height: calc(100vh - 130px);
            min-height: 600px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .flow-palette {
            width: 160px;
            flex-shrink: 0;
            background: #1a1a2e;
            border-right: 1px solid rgba(255,255,255,0.08);
            padding: 14px 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .palette-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#888; margin-bottom:4px; padding-left:4px; }
        .palette-node { border-radius:8px; padding:8px 10px; font-size:12px; font-weight:600; cursor:grab; user-select:none; display:flex; align-items:center; gap:7px; transition:opacity .15s; }
        .palette-node:hover { opacity:.85; }
        .pn-start   { background:rgba(34,197,94,.15);  border:1px solid rgba(34,197,94,.4);  color:#4ade80; }
        .pn-message { background:rgba(59,130,246,.15); border:1px solid rgba(59,130,246,.4); color:#60a5fa; }
        .pn-wait    { background:rgba(234,179,8,.15);  border:1px solid rgba(234,179,8,.4);  color:#facc15; }
        .pn-buttons { background:rgba(168,85,247,.15); border:1px solid rgba(168,85,247,.4); color:#c084fc; }
        .pn-size    { background:rgba(249,115,22,.15); border:1px solid rgba(249,115,22,.4); color:#fb923c; }
        .pn-total   { background:rgba(20,184,166,.15); border:1px solid rgba(20,184,166,.4); color:#2dd4bf; }
        .pn-action  { background:rgba(239,68,68,.15);  border:1px solid rgba(239,68,68,.4);  color:#f87171; }
        .pn-end     { background:rgba(107,114,128,.15);border:1px solid rgba(107,114,128,.4);color:#9ca3af; }
        .palette-divider { height:1px; background:rgba(255,255,255,.06); margin:4px 0; }
        .palette-spacer { flex:1; }
        .btn-save  { background:#f59e0b; color:#000; border:none; border-radius:8px; padding:9px 14px; font-size:12px; font-weight:700; cursor:pointer; width:100%; }
        .btn-save:hover { background:#d97706; }
        .btn-reset { background:transparent; border:1px solid rgba(239,68,68,.4); color:#f87171; border-radius:8px; padding:7px 14px; font-size:11px; font-weight:600; cursor:pointer; width:100%; margin-top:4px; }
        .btn-reset:hover { background:rgba(239,68,68,.1); }
        .btn-export  { background:rgba(96,165,250,.12); border:1px solid rgba(96,165,250,.35); color:#60a5fa; border-radius:8px; padding:7px 14px; font-size:11px; font-weight:600; cursor:pointer; width:100%; margin-top:4px; }
        .btn-export:hover  { background:rgba(96,165,250,.22); }
        .btn-import  { background:rgba(52,211,153,.12); border:1px solid rgba(52,211,153,.35); color:#34d399; border-radius:8px; padding:7px 14px; font-size:11px; font-weight:600; cursor:pointer; width:100%; margin-top:4px; }
        .btn-import:hover  { background:rgba(52,211,153,.22); }
        .btn-example { background:rgba(168,85,247,.12); border:1px solid rgba(168,85,247,.35); color:#c084fc; border-radius:8px; padding:7px 14px; font-size:11px; font-weight:600; cursor:pointer; width:100%; margin-top:4px; }
        .btn-example:hover { background:rgba(168,85,247,.22); }

        #drawflow {
            flex: 1 1 0;
            min-width: 0;
            position: relative;
            background: #0d0d1a;
            background-image: radial-gradient(circle, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        /* Make Drawflow's inner canvas fill the mount element */
        #drawflow .drawflow {
            width: 100% !important;
            min-height: 100% !important;
        }
        .drawflow .connection .main-path { stroke:rgba(248,197,78,.6); stroke-width:2.5px; }
        .drawflow .connection.selected .main-path { stroke:#f8c54e; }
        .drawflow-node { background:#1e1e32 !important; border:1.5px solid rgba(255,255,255,.1) !important; border-radius:10px !important; box-shadow:0 4px 16px rgba(0,0,0,.4) !important; padding:0 !important; min-width:170px; }
        .drawflow-node.selected { border-color:#f59e0b !important; box-shadow:0 0 0 3px rgba(245,158,11,.25),0 4px 16px rgba(0,0,0,.4) !important; }
        .drawflow-node .inputs .input, .drawflow-node .outputs .output { background:#f59e0b !important; border-color:#d97706 !important; width:11px; height:11px; }
        .df-node { padding:10px 12px; }
        .df-type { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px; opacity:.7; }
        .df-label { font-size:13px; font-weight:600; color:#e5e7eb; margin-bottom:3px; }
        .df-sub { font-size:10px; font-family:monospace; color:#6b7280; }
        .df-out-tag { font-size:9px; background:rgba(255,255,255,.05); border-radius:4px; padding:2px 5px; color:#9ca3af; margin-top:2px; }
        .node-start   .df-type { color:#4ade80; }
        .node-send_message .df-type { color:#60a5fa; }
        .node-wait_image .df-type { color:#facc15; }
        .node-send_buttons .df-type { color:#c084fc; }
        .node-select_size .df-type { color:#fb923c; }
        .node-show_order_total .df-type { color:#2dd4bf; }
        .node-action .df-type { color:#f87171; }
        .node-end .df-type { color:#9ca3af; }

        .flow-props { width:240px; flex-shrink:0; background:#1a1a2e; border-left:1px solid rgba(255,255,255,.08); display:flex; flex-direction:column; overflow:hidden; }
        .props-header { padding:14px 14px 10px; border-bottom:1px solid rgba(255,255,255,.08); font-size:12px; font-weight:700; color:#f59e0b; display:flex; align-items:center; justify-content:space-between; }
        .props-scroll { flex:1; overflow-y:auto; padding:14px; display:flex; flex-direction:column; gap:12px; }
        .prop-group label { display:block; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; margin-bottom:4px; }
        .prop-input { width:100%; background:#0d0d1a; border:1px solid rgba(255,255,255,.1); border-radius:6px; padding:7px 10px; font-size:12px; color:#e5e7eb; outline:none; box-sizing:border-box; }
        .prop-input:focus { border-color:#f59e0b; }
        .prop-hint { font-size:10px; color:#4b5563; margin-top:3px; }
        .btn-section { border-top:1px solid rgba(255,255,255,.06); padding:12px 14px; display:flex; gap:8px; }
        .btn-apply { flex:1; background:#f59e0b; color:#000; border:none; border-radius:7px; padding:8px; font-size:12px; font-weight:700; cursor:pointer; }
        .btn-apply:hover { background:#d97706; }
        .btn-del { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.4); color:#f87171; border-radius:7px; padding:8px 12px; font-size:12px; cursor:pointer; }
        .btn-add-btn { background:rgba(168,85,247,.15); border:1px solid rgba(168,85,247,.3); color:#c084fc; border-radius:6px; padding:5px 10px; font-size:11px; cursor:pointer; margin-top:4px; }
        .empty-state { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; color:#374151; padding:20px; text-align:center; }
        ::-webkit-scrollbar { width:5px; } ::-webkit-scrollbar-track { background:transparent; } ::-webkit-scrollbar-thumb { background:rgba(255,255,255,.1); border-radius:3px; }
    </style>

    <div
        class="flow-wrap"
        x-data="flowBuilder(@js($this->flowData), @js($this->botMessages))"
        @drop.prevent="onDrop($event)"
        @dragover.prevent
    >
        {{-- ── LEFT PALETTE ── --}}
        <div class="flow-palette">
            <div class="palette-title">Nodes</div>
            <div class="palette-node pn-start"   draggable="true" @dragstart="dragStart($event,'start')">🚀 Start</div>
            <div class="palette-node pn-message"  draggable="true" @dragstart="dragStart($event,'send_message')">💬 Send Message</div>
            <div class="palette-node pn-wait"     draggable="true" @dragstart="dragStart($event,'wait_image')">📷 Wait Images</div>
            <div class="palette-node pn-buttons"  draggable="true" @dragstart="dragStart($event,'send_buttons')">🔘 Buttons</div>
            <div class="palette-node pn-size"     draggable="true" @dragstart="dragStart($event,'select_size')">📐 Select Size</div>
            <div class="palette-node pn-total"    draggable="true" @dragstart="dragStart($event,'show_order_total')">💰 Show Total</div>
            <div class="palette-node pn-action"   draggable="true" @dragstart="dragStart($event,'action')">⚙️ Action</div>
            <div class="palette-node pn-end"      draggable="true" @dragstart="dragStart($event,'end')">🏁 End</div>
            <div class="palette-divider"></div>
            <div style="font-size:10px; color:#4b5563; padding:0 4px; line-height:1.6;">Drag nodes to canvas. Connect output → input dots. Click to edit.</div>
            <div class="palette-spacer"></div>
            <button class="btn-save"    @click="saveFlow()">💾 Save Flow</button>
            <button class="btn-export"  @click="exportFlow()">📤 Export JSON</button>
            <button class="btn-import"  @click="triggerImport()">📥 Import JSON</button>
            <button class="btn-example" @click="loadExample()">📋 Load Example</button>
            <button class="btn-reset"   @click="resetFlow()">🗑 Reset</button>
            <input type="file" accept=".json" x-ref="importFile" style="display:none" @change="onImportFile($event)">
        </div>

        {{-- ── CENTER CANVAS ── --}}
        <div id="drawflow"></div>

        {{-- ── RIGHT PROPERTIES ── --}}
        <div class="flow-props">
            <template x-if="!selectedNode">
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:40px;height:40px;opacity:.3;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.828 1.172H7v-2a4 4 0 011.172-2.828z"/></svg>
                    <p style="font-size:12px;">Click a node to<br>edit its properties</p>
                </div>
            </template>

            <template x-if="selectedNode">
                <div style="display:flex;flex-direction:column;height:100%;overflow:hidden;">
                    <div class="props-header">
                        <span x-text="nodeTypeLabel(editData.node_type)"></span>
                        <span style="font-size:10px;color:#6b7280;" x-text="'#'+selectedNodeId"></span>
                    </div>
                    <div class="props-scroll">

                        <div class="prop-group">
                            <label>Label</label>
                            <input class="prop-input" type="text" x-model="editData.label" placeholder="Node label">
                        </div>

                        <template x-if="['send_message','wait_image','end'].includes(editData.node_type)">
                            <div class="prop-group">
                                <label>Message Key</label>
                                <select class="prop-input" x-model="editData.message_key">
                                    <option value="">— none —</option>
                                    <template x-for="msg in botMessages" :key="msg.key">
                                        <option :value="msg.key" x-text="msg.key"></option>
                                    </template>
                                </select>
                                <p class="prop-hint" x-show="editData.message_key" x-text="msgPreview(editData.message_key)"></p>
                            </div>
                        </template>

                        <template x-if="editData.node_type === 'send_buttons'">
                            <div>
                                <div class="prop-group">
                                    <label>Message Key</label>
                                    <select class="prop-input" x-model="editData.message_key">
                                        <option value="">— none —</option>
                                        <template x-for="msg in botMessages" :key="msg.key">
                                            <option :value="msg.key" x-text="msg.key"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="prop-group" style="margin-top:10px;">
                                    <label>Buttons (max 3)</label>
                                    <template x-for="(btn,i) in (editData.buttons||[])" :key="i">
                                        <div style="display:flex;gap:5px;margin-bottom:5px;">
                                            <input class="prop-input" type="text" placeholder="ID" x-model="btn.id" style="flex:1;">
                                            <input class="prop-input" type="text" placeholder="Title" x-model="btn.title" style="flex:1.5;">
                                            <button @click="removeButton(i)" style="background:transparent;border:none;color:#f87171;cursor:pointer;font-size:16px;padding:0 4px;">×</button>
                                        </div>
                                    </template>
                                    <button class="btn-add-btn" @click="addButton()" x-show="(editData.buttons||[]).length<3">+ Add Button</button>
                                </div>
                            </div>
                        </template>

                        <template x-if="editData.node_type==='action'">
                            <div class="prop-group">
                                <label>Action Type</label>
                                <select class="prop-input" x-model="editData.action">
                                    <option value="">— choose —</option>
                                    <option value="create_payment_link">💳 Create Payment Link</option>
                                    <option value="mark_store_payment">🏪 Mark Pay in Store</option>
                                    <option value="mark_completed">✅ Mark Completed</option>
                                    <option value="reset_order">🔄 Reset Order</option>
                                </select>
                            </div>
                        </template>

                        <template x-if="hasOutputLabels(editData.node_type) && editData.output_labels">
                            <div class="prop-group">
                                <label>Output Triggers</label>
                                <template x-for="(cfg,outName) in editData.output_labels" :key="outName">
                                    <div style="background:rgba(255,255,255,.03);border-radius:6px;padding:8px;margin-bottom:6px;">
                                        <div style="font-size:10px;color:#f59e0b;margin-bottom:5px;" x-text="outName"></div>
                                        <select class="prop-input" style="margin-bottom:4px;" x-model="cfg.trigger">
                                            <option value="any">Any input</option>
                                            <option value="image">📷 Image received</option>
                                            <option value="text:finish">✅ Finish keyword</option>
                                            <option value="text:*">Any text</option>
                                            <option value="size_selected">📐 Size selected</option>
                                            <option value="button:confirm_yes">✅ Confirm Yes</option>
                                            <option value="button:confirm_no">❌ Confirm No</option>
                                            <template x-for="btn in (editData.buttons||[])" :key="btn.id">
                                                <option :value="'button:'+btn.id" x-text="'🔘 '+btn.title"></option>
                                            </template>
                                        </select>
                                        <input class="prop-input" type="text" placeholder="Edge label" x-model="cfg.description">
                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>
                    <div class="btn-section">
                        <button class="btn-apply" @click="applyProperties()">Apply Changes</button>
                        <button class="btn-del"   @click="deleteNode()">🗑</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/drawflow@0.0.59/dist/drawflow.min.js"></script>
    <script>
    function flowBuilder(initialData, botMessages) {
        return {
            editor: null,
            selectedNodeId: null,
            selectedNode: null,
            editData: {},
            botMessages: botMessages,

            init() {
                const el = document.getElementById('drawflow');
                this.editor = new Drawflow(el);
                this.editor.reroute = true;
                this.editor.reroute_fix_curvature = true;
                this.editor.start();

                if (initialData && initialData.drawflow) {
                    this.editor.import(initialData);
                }

                this.editor.on('nodeSelected', (id) => {
                    this.selectedNodeId = id;
                    this.selectedNode   = this.editor.getNodeFromId(id);
                    this.editData       = JSON.parse(JSON.stringify(this.selectedNode.data));
                    if (!this.editData.buttons)       this.editData.buttons = [];
                    if (!this.editData.output_labels) this.editData.output_labels = this.defaultOutputLabels(this.editData.node_type, this.editData.buttons);
                });
                this.editor.on('nodeUnselected', () => { this.selectedNodeId = null; this.selectedNode = null; this.editData = {}; });
                this.editor.on('nodeRemoved',    () => { this.selectedNodeId = null; this.selectedNode = null; });
            },

            dragStart(event, type) { event.dataTransfer.setData('nodeType', type); },

            onDrop(event) {
                const type = event.dataTransfer.getData('nodeType');
                if (!type) return;
                const rect = document.getElementById('drawflow').getBoundingClientRect();
                const zoom = this.editor.zoom;
                const x = (event.clientX - rect.left - this.editor.canvas_x) / zoom;
                const y = (event.clientY - rect.top  - this.editor.canvas_y) / zoom;
                this.addNode(type, x - 85, y - 35);
            },

            addNode(type, x, y) {
                const nodeHtml = {
                    start:            `<div class="df-node node-start"><div class="df-type">🚀 Start</div><div class="df-label" df-label>Start</div></div>`,
                    send_message:     `<div class="df-node node-send_message"><div class="df-type">💬 Send Message</div><div class="df-label" df-label>Send Message</div><div class="df-sub" df-message_key></div></div>`,
                    wait_image:       `<div class="df-node node-wait_image"><div class="df-type">📷 Wait Images</div><div class="df-label" df-label>Wait for Images</div><div class="df-out-tag">→ 1: finish keyword</div></div>`,
                    send_buttons:     `<div class="df-node node-send_buttons"><div class="df-type">🔘 Buttons</div><div class="df-label" df-label>Send Buttons</div><div class="df-sub" df-message_key></div></div>`,
                    select_size:      `<div class="df-node node-select_size"><div class="df-type">📐 Select Size</div><div class="df-label" df-label>Select Print Size</div><div class="df-out-tag">→ 1: size selected</div></div>`,
                    show_order_total: `<div class="df-node node-show_order_total"><div class="df-type">💰 Show Total</div><div class="df-label" df-label>Show Order Total</div><div class="df-out-tag">→ 1: confirm &nbsp;→ 2: cancel</div></div>`,
                    action:           `<div class="df-node node-action"><div class="df-type">⚙️ Action</div><div class="df-label" df-label>Action</div><div class="df-sub" df-action></div></div>`,
                    end:              `<div class="df-node node-end"><div class="df-type">🏁 End</div><div class="df-label" df-label>End</div></div>`,
                };
                const cfg = {
                    start:            { i:0, o:1, d:{ node_type:'start', label:'Start', output_labels:{ output_1:{ trigger:'any', description:'→ Next' } } } },
                    send_message:     { i:1, o:1, d:{ node_type:'send_message', label:'Send Message', message_key:'', output_labels:{ output_1:{ trigger:'any', description:'→ Next' } } } },
                    wait_image:       { i:1, o:1, d:{ node_type:'wait_image', label:'Wait for Images', message_key:'welcome', output_labels:{ output_1:{ trigger:'text:finish', description:'✅ Finish' } } } },
                    send_buttons:     { i:1, o:2, d:{ node_type:'send_buttons', label:'Send Buttons', message_key:'', buttons:[{id:'btn_yes',title:'Yes'},{id:'btn_no',title:'No'}], output_labels:{ output_1:{trigger:'button:btn_yes',description:'Yes'}, output_2:{trigger:'button:btn_no',description:'No'} } } },
                    select_size:      { i:1, o:1, d:{ node_type:'select_size', label:'Select Print Size', output_labels:{ output_1:{ trigger:'size_selected', description:'📐 Size chosen' } } } },
                    show_order_total: { i:1, o:2, d:{ node_type:'show_order_total', label:'Show Order Total', output_labels:{ output_1:{trigger:'button:confirm_yes',description:'✅ Confirmed'}, output_2:{trigger:'button:confirm_no',description:'❌ Cancelled'} } } },
                    action:           { i:1, o:1, d:{ node_type:'action', label:'Action', action:'create_payment_link', output_labels:{ output_1:{ trigger:'any', description:'→ Next' } } } },
                    end:              { i:1, o:0, d:{ node_type:'end', label:'End', message_key:'' } },
                }[type];
                if (!cfg) return;
                this.editor.addNode(type, cfg.i, cfg.o, x, y, 'node-' + type, cfg.d, nodeHtml[type]);
            },

            applyProperties() {
                if (!this.selectedNodeId) return;
                if (this.editData.node_type === 'send_buttons') {
                    const labels = {};
                    (this.editData.buttons || []).forEach((btn, i) => {
                        const k = 'output_' + (i+1);
                        labels[k] = this.editData.output_labels?.[k] || { trigger:'button:'+btn.id, description:btn.title };
                    });
                    this.editData.output_labels = labels;
                }
                this.editor.updateNodeDataFromId(this.selectedNodeId, { ...this.editData });
                this.selectedNode = this.editor.getNodeFromId(this.selectedNodeId);
            },

            deleteNode() { if (this.selectedNodeId) this.editor.removeNodeId('node-' + this.selectedNodeId); },

            addButton() {
                if (!this.editData.buttons) this.editData.buttons = [];
                if (this.editData.buttons.length >= 3) return;
                const n = this.editData.buttons.length + 1;
                this.editData.buttons.push({ id:'btn_'+n, title:'Button '+n });
            },
            removeButton(i) { this.editData.buttons.splice(i, 1); },

            saveFlow() { const data = this.editor.export(); @this.saveFlow(data); },
            resetFlow() {
                if (!confirm('Reset the entire flow? This cannot be undone.')) return;
                @this.resetFlow();
                setTimeout(() => window.location.reload(), 1400);
            },

            exportFlow() {
                const data = this.editor.export();
                const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                a.href = url; a.download = 'bot-flow.json'; a.click();
                URL.revokeObjectURL(url);
            },

            triggerImport() { this.$refs.importFile.value = ''; this.$refs.importFile.click(); },

            onImportFile(event) {
                const file = event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const data = JSON.parse(e.target.result);
                        if (!data.drawflow) { alert('Invalid flow file — missing "drawflow" key.'); return; }
                        if (!confirm('This will replace the current canvas. Continue?')) return;
                        this.editor.clear();
                        this.editor.import(data);
                    } catch (err) {
                        alert('Could not parse JSON: ' + err.message);
                    }
                };
                reader.readAsText(file);
            },

            loadExample() {
                if (!confirm('Load the example photo-print flow? This will replace the current canvas.')) return;
                this.editor.clear();
                const nhIcons = {start:'🚀 Start',send_message:'💬 Send Message',wait_image:'📷 Wait Images',send_buttons:'🔘 Buttons',select_size:'📐 Select Size',show_order_total:'💰 Show Total',action:'⚙️ Action',end:'🏁 End'};
                const nh = function(type, label, sub) {
                    return '<div class="df-node node-' + type + '"><div class="df-type">' + (nhIcons[type]||type) + '</div><div class="df-label">' + label + '</div>' + (sub ? '<div class="df-sub">' + sub + '</div>' : '') + '</div>';
                };
                const flow = {
                    drawflow: { Home: { data: {
                        "1":  { id:1,  name:"start",            data:{ node_type:"start",            label:"Start",                output_labels:{ output_1:{trigger:"any",             description:"→ Next"          } } },                                                                                                                              class:"node-start",            html:nh("start","Start"),                              typenode:false, inputs:{},                                                              outputs:{ output_1:{ connections:[{node:"2",output:"input_1"}] } },                                                              pos_x:50,   pos_y:300 },
                        "2":  { id:2,  name:"wait_image",       data:{ node_type:"wait_image",       label:"Wait for Images",      message_key:"welcome",                output_labels:{ output_1:{trigger:"text:finish", description:"✅ Finish"         } } },                                                                                    class:"node-wait_image",       html:nh("wait_image","Wait for Images","welcome"),      typenode:false, inputs:{ input_1:{ connections:[{node:"1",input:"output_1"}] } },  outputs:{ output_1:{ connections:[{node:"3",output:"input_1"}] } },                                                              pos_x:290,  pos_y:300 },
                        "3":  { id:3,  name:"select_size",      data:{ node_type:"select_size",      label:"Select Print Size",    output_labels:{ output_1:{trigger:"size_selected",  description:"📐 Size chosen"    } } },                                                                                                                         class:"node-select_size",      html:nh("select_size","Select Print Size"),             typenode:false, inputs:{ input_1:{ connections:[{node:"2",input:"output_1"}] } },  outputs:{ output_1:{ connections:[{node:"4",output:"input_1"}] } },                                                              pos_x:520,  pos_y:300 },
                        "4":  { id:4,  name:"show_order_total", data:{ node_type:"show_order_total", label:"Show Order Total",     output_labels:{ output_1:{trigger:"button:confirm_yes",description:"✅ Confirmed"}, output_2:{trigger:"button:confirm_no",description:"❌ Cancelled"} } },                                                        class:"node-show_order_total", html:nh("show_order_total","Show Order Total"),         typenode:false, inputs:{ input_1:{ connections:[{node:"3",input:"output_1"}] } },  outputs:{ output_1:{ connections:[{node:"5",output:"input_1"}] }, output_2:{ connections:[{node:"6",output:"input_1"}] } },       pos_x:750,  pos_y:300 },
                        "5":  { id:5,  name:"send_buttons",     data:{ node_type:"send_buttons",     label:"Choose Payment",       message_key:"choose_payment_method",  buttons:[{id:"pay_card",title:"Credit Card 💳"},{id:"pay_store",title:"Pay in Store 🏪"}], output_labels:{ output_1:{trigger:"button:pay_card",description:"💳 Card"}, output_2:{trigger:"button:pay_store",description:"🏪 Store"} } }, class:"node-send_buttons",     html:nh("send_buttons","Choose Payment","choose_payment_method"), typenode:false, inputs:{ input_1:{ connections:[{node:"4",input:"output_1"}] } },  outputs:{ output_1:{ connections:[{node:"7",output:"input_1"}] }, output_2:{ connections:[{node:"8",output:"input_1"}] } },       pos_x:980,  pos_y:200 },
                        "6":  { id:6,  name:"send_message",     data:{ node_type:"send_message",     label:"Order Cancelled",      message_key:"order_cancelled",        output_labels:{ output_1:{trigger:"any",             description:"→ End"            } } },                                                                                 class:"node-send_message",     html:nh("send_message","Order Cancelled","order_cancelled"), typenode:false, inputs:{ input_1:{ connections:[{node:"4",input:"output_2"}] } },  outputs:{ output_1:{ connections:[{node:"11",output:"input_1"}] } },                                                             pos_x:980,  pos_y:490 },
                        "7":  { id:7,  name:"action",           data:{ node_type:"action",           label:"Create Payment Link",  action:"create_payment_link",         output_labels:{ output_1:{trigger:"any",             description:"→ Next"          } } },                                                                                  class:"node-action",           html:nh("action","Create Payment Link","create_payment_link"), typenode:false, inputs:{ input_1:{ connections:[{node:"5",input:"output_1"}] } },  outputs:{ output_1:{ connections:[{node:"9",output:"input_1"}] } },                                                              pos_x:1210, pos_y:130 },
                        "8":  { id:8,  name:"action",           data:{ node_type:"action",           label:"Pay in Store",         action:"mark_store_payment",          output_labels:{ output_1:{trigger:"any",             description:"→ Next"          } } },                                                                                  class:"node-action",           html:nh("action","Pay in Store","mark_store_payment"),  typenode:false, inputs:{ input_1:{ connections:[{node:"5",input:"output_2"}] } },  outputs:{ output_1:{ connections:[{node:"10",output:"input_1"}] } },                                                             pos_x:1210, pos_y:290 },
                        "9":  { id:9,  name:"end",              data:{ node_type:"end",              label:"Payment Link Sent",    message_key:"payment_link_sent" },                                                                                                                                                                                class:"node-end",              html:nh("end","Payment Link Sent"),                    typenode:false, inputs:{ input_1:{ connections:[{node:"7",input:"output_1"}] } },  outputs:{},                                                                                                                                              pos_x:1440, pos_y:130 },
                        "10": { id:10, name:"end",              data:{ node_type:"end",              label:"Store Pay Confirmed",  message_key:"pay_store_confirmation" },                                                                                                                                                                          class:"node-end",              html:nh("end","Store Pay Confirmed"),                  typenode:false, inputs:{ input_1:{ connections:[{node:"8",input:"output_1"}] } },  outputs:{},                                                                                                                                              pos_x:1440, pos_y:290 },
                        "11": { id:11, name:"end",              data:{ node_type:"end",              label:"Cancelled",            message_key:"order_cancelled" },                                                                                                                                                                                 class:"node-end",              html:nh("end","Cancelled"),                            typenode:false, inputs:{ input_1:{ connections:[{node:"6",input:"output_1"}] } },  outputs:{},                                                                                                                                              pos_x:1210, pos_y:490 },
                    } } }
                };
                this.editor.import(flow);
            },

            msgPreview(key) {
                const m = this.botMessages.find(m => m.key === key);
                return m ? m.preview : '';
            },

            nodeTypeLabel(t) {
                return { start:'🚀 Start', send_message:'💬 Send Message', wait_image:'📷 Wait Images', send_buttons:'🔘 Buttons', select_size:'📐 Select Size', show_order_total:'💰 Show Total', action:'⚙️ Action', end:'🏁 End' }[t] || t;
            },

            hasOutputLabels(t) { return ['start','send_message','wait_image','send_buttons','select_size','show_order_total','action'].includes(t); },

            defaultOutputLabels(type, buttons = []) {
                const m = { start:{output_1:{trigger:'any',description:'→ Next'}}, send_message:{output_1:{trigger:'any',description:'→ Next'}}, wait_image:{output_1:{trigger:'text:finish',description:'✅ Finish'}}, select_size:{output_1:{trigger:'size_selected',description:'📐 Size chosen'}}, show_order_total:{output_1:{trigger:'button:confirm_yes',description:'✅ Confirmed'},output_2:{trigger:'button:confirm_no',description:'❌ Cancelled'}}, action:{output_1:{trigger:'any',description:'→ Next'}} };
                if (type === 'send_buttons') {
                    const r = {};
                    (buttons.length ? buttons : [{id:'btn_1',title:'Btn 1'}]).forEach((b,i) => { r['output_'+(i+1)] = { trigger:'button:'+b.id, description:b.title }; });
                    return r;
                }
                return m[type] || {};
            },
        };
    }
    </script>

</x-filament-panels::page>
