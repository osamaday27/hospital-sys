<!-- Blade View for Ophthalmology Documentation - Unified Drawing View -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0"  style="color: #fff">Ophthalmology Documentation - Drawing Interface</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Tools Section -->
                <div class="col-md-12 text-center mb-4">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button class="btn btn-outline-primary" onclick="setTool('pen')">✏️ Draw</button>
                        <button class="btn btn-outline-secondary" onclick="setTool('circle')">⭕ Circle</button>
                        <button class="btn btn-outline-dark" onclick="setTool('line')">📏 Line</button>
                        <button class="btn btn-outline-info" onclick="setTool('text')">🔤 Text</button>
                        <button class="btn btn-outline-danger" onclick="clearAllCanvases()">❌ Clear All</button>
                        <input type="color" id="colorPicker" value="#ff0000" onchange="setColor(this.value)">
                        <input type="range" id="sizePicker" min="1" max="10" value="2" onchange="setSize(this.value)">
                        <button class="btn btn-success" onclick="downloadAllAsImage()">📥 Download All</button>
                        <button class="btn btn-dark" onclick="downloadPDF()">📄 Export PDF</button>
                        <button class="btn btn-warning" onclick="copyCanvas('rightCorneaCanvas', 'leftCorneaCanvas')">📋 Copy Cornea ➡️</button>
                    </div>
                </div>

                <!-- Right Eye Sections -->
                <div class="col-md-6 text-center mb-4">
                  
                    <h6 class="text-primary">Right Eye - Cornea</h6>
                    <div class="position-relative d-inline-block mb-4">
                        <img src="{{ asset('assets/images/OPTH/cornea_rt.png') }}" class="img-fluid rounded shadow-sm" style="width: 450px;height: 450px;">
                        <canvas id="rightCorneaCanvas" class="position-absolute" width="450" height="450"></canvas>
                    </div>
                    <h6 class="text-primary">Right Eye - Posterior Segment</h6>
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('assets/images/OPTH/post_seg_rt.png') }}" class="img-fluid rounded shadow-sm" style="width: 450px;height: 450px;">
                        <canvas id="rightPostCanvas" class="position-absolute" width="450" height="450"></canvas>
                    </div>
                </div>

                <!-- Left Eye Sections -->
                <div class="col-md-6 text-center mb-4">
                    
                    <h6 class="text-primary">Left Eye - Cornea</h6>
                    <div class="position-relative d-inline-block mb-4">
                        <img src="{{ asset('assets/images/OPTH/cornea_lt.png') }}" class="img-fluid rounded shadow-sm" style="width: 450px;height: 450px;">
                        <canvas id="leftCorneaCanvas" class="position-absolute" width="450" height="450"></canvas>
                    </div>
                    <h6 class="text-primary">Left Eye - Posterior Segment</h6>
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('assets/images/OPTH/post_seg_lt.png') }}" class="img-fluid rounded shadow-sm" style="width: 450px;height: 450px;">
                        <canvas id="leftPostCanvas" class="position-absolute" width="450" height="450"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
    canvas {
        position: absolute;
        top: 0;
        left: 0;
        cursor: crosshair;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    let currentTool = 'pen';
    let drawColor = '#ff0000';
    let drawSize = 2;

    function setTool(tool) { currentTool = tool; }
    function setColor(color) { drawColor = color; }
    function setSize(size) { drawSize = size; }

    function setupCanvas(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let startX, startY;

        canvas.addEventListener('mousedown', (e) => {
            const rect = canvas.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
            isDrawing = true;
            if (currentTool === 'pen') {
                ctx.beginPath();
                ctx.moveTo(startX, startY);
            }
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            ctx.strokeStyle = drawColor;
            ctx.lineWidth = drawSize;
            ctx.lineCap = 'round';

            if (currentTool === 'pen') {
                ctx.lineTo(x, y);
                ctx.stroke();
            }
        });

        canvas.addEventListener('mouseup', (e) => {
            if (!isDrawing) return;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            isDrawing = false;
            ctx.strokeStyle = drawColor;
            ctx.lineWidth = drawSize;

            if (currentTool === 'line') {
                ctx.beginPath();
                ctx.moveTo(startX, startY);
                ctx.lineTo(x, y);
                ctx.stroke();
            } else if (currentTool === 'circle') {
                const radius = Math.sqrt(Math.pow(x - startX, 2) + Math.pow(y - startY, 2));
                ctx.beginPath();
                ctx.arc(startX, startY, radius, 0, Math.PI * 2);
                ctx.stroke();
            } else if (currentTool === 'text') {
                const text = prompt("Enter text:");
                if (text) {
                    ctx.font = `${drawSize * 6}px Arial`;
                    ctx.fillStyle = drawColor;
                    ctx.fillText(text, startX, startY);
                }
            }
        });
    }

    function clearAllCanvases() {
        [
         
            'rightCorneaCanvas', 'leftCorneaCanvas',
            'rightPostCanvas', 'leftPostCanvas'
        ].forEach(id => {
            const canvas = document.getElementById(id);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
    }

    function copyCanvas(fromId, toId) {
        const fromCanvas = document.getElementById(fromId);
        const toCanvas = document.getElementById(toId);
        const toCtx = toCanvas.getContext('2d');
        toCtx.drawImage(fromCanvas, 0, 0);
    }

    function downloadAllAsImage() {
        html2canvas(document.querySelector('.card-body')).then(canvas => {
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'ophthalmology_drawings.png';
            link.click();
        });
    }

    function downloadPDF() {
        html2canvas(document.querySelector('.card-body')).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
            const width = pdf.internal.pageSize.getWidth();
            const height = (canvas.height * width) / canvas.width;
            pdf.addImage(imgData, 'PNG', 0, 0, width, height);
            pdf.save('ophthalmology_drawings.pdf');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        [
         
            'rightCorneaCanvas', 'leftCorneaCanvas',
            'rightPostCanvas', 'leftPostCanvas'
        ].forEach(setupCanvas);
    });
</script>
