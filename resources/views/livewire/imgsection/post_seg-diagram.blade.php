<div class="container">
    <!-- ✅ قسم القرنية -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0" style="color: #fff">Ophthalmology Documentation - Cornea & Anterior Segment</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- ✅ أدوات الرسم للقرنية -->
                <div class="col-md-12 text-center mb-3">
                    <h6 class="text-secondary">Drawing Tools - Cornea</h6>
                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-outline-primary" onclick="CorneaDrawing.setTool('pen')">✏️ Draw</button>
                        <button class="btn btn-outline-secondary" onclick="CorneaDrawing.setTool('circle')">⭕ Circle</button>
                        <button class="btn btn-outline-dark" onclick="CorneaDrawing.setTool('line')">📏 Line</button>
                        <button class="btn btn-outline-danger" onclick="CorneaDrawing.clearCanvas()">❌ Clear</button>
                        <input type="color" id="corneaColorPicker" value="#ff0000" onchange="CorneaDrawing.setColor(this.value)">
                        <input type="range" id="corneaSizePicker" min="1" max="10" value="2" onchange="CorneaDrawing.setSize(this.value)">
                    </div>
                </div>

                <!-- ✅ قرنية العين اليمنى -->
                <div class="col-md-6 text-center">
                    <h6 class="text-primary">Right Eye - Cornea</h6>
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('assets/images/OPTH/cornea_rt.png') }}" class="img-fluid rounded shadow-sm">
                        <canvas id="rightCorneaCanvas" class="position-absolute"></canvas>
                    </div>
                </div>

                <!-- ✅ قرنية العين اليسرى -->
                <div class="col-md-6 text-center">
                    <h6 class="text-primary">Left Eye - Cornea</h6>
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('assets/images/OPTH/cornea.png') }}" class="img-fluid rounded shadow-sm">
                        <canvas id="leftCorneaCanvas" class="position-absolute"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ قسم الجزء الخلفي -->
    <div class="card shadow-lg border-0 mt-4">
        <div class="card-header bg-secondary text-white text-center">
            <h5 class="mb-0">Ophthalmology Documentation - Posterior Segment</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- ✅ أدوات الرسم للجزء الخلفي -->
                <div class="col-md-12 text-center mb-3">
                    <h6 class="text-secondary">Drawing Tools - Posterior Segment</h6>
                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-outline-primary" onclick="PostSegDrawing.setTool('pen')">✏️ Draw</button>
                        <button class="btn btn-outline-secondary" onclick="PostSegDrawing.setTool('circle')">⭕ Circle</button>
                        <button class="btn btn-outline-dark" onclick="PostSegDrawing.setTool('line')">📏 Line</button>
                        <button class="btn btn-outline-danger" onclick="PostSegDrawing.clearCanvas()">❌ Clear</button>
                        <input type="color" id="postColorPicker" value="#ff0000" onchange="PostSegDrawing.setColor(this.value)">
                        <input type="range" id="postSizePicker" min="1" max="10" value="2" onchange="PostSegDrawing.setSize(this.value)">
                    </div>
                </div>

                <!-- ✅ الجزء الخلفي للعين اليمنى -->
                <div class="col-md-6 text-center">
                    <h6 class="text-primary">Right Eye - Posterior Segment</h6>
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('assets/images/OPTH/post_seg_rt.png') }}" class="img-fluid rounded shadow-sm">
                        <canvas id="rightPostSegCanvas" class="position-absolute"></canvas>
                    </div>
                </div>

                <!-- ✅ الجزء الخلفي للعين اليسرى -->
                <div class="col-md-6 text-center">
                    <h6 class="text-primary">Left Eye - Posterior Segment</h6>
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('assets/images/OPTH/post_seg_lt.png') }}" class="img-fluid rounded shadow-sm">
                        <canvas id="leftPostSegCanvas" class="position-absolute"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ تحسينات CSS -->
<style>
    canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        cursor: crosshair;
    }
</style>

<!-- ✅ تحسينات JavaScript -->
<script>
    function createDrawingManager(drawingNamespace) {
        return (() => {
            let tool = "pen";
            let drawColor = "#ff0000";
            let drawSize = 2;
            let isDrawing = false;
            let startX, startY;
            let drawings = {};

            function setTool(selectedTool) { tool = selectedTool; }
            function setColor(color) { drawColor = color; }
            function setSize(size) { drawSize = size; }

            function initializeCanvas(canvasId) {
                const canvas = document.getElementById(canvasId);
                const ctx = canvas.getContext("2d");
                drawings[canvasId] = [];

                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;

                canvas.addEventListener("mousedown", (e) => {
                    isDrawing = true;
                    startX = e.offsetX;
                    startY = e.offsetY;
                    if (tool === "pen") {
                        ctx.beginPath();
                        ctx.moveTo(startX, startY);
                    }
                });

                canvas.addEventListener("mousemove", (e) => {
                    if (!isDrawing) return;
                    ctx.strokeStyle = drawColor;
                    ctx.lineWidth = drawSize;
                    ctx.lineJoin = "round";
                    ctx.lineCap = "round";

                    if (tool === "pen") {
                        ctx.lineTo(e.offsetX, e.offsetY);
                        ctx.stroke();
                    }
                });

                canvas.addEventListener("mouseup", (e) => {
                    isDrawing = false;
                    drawings[canvasId].push({ tool, color: drawColor, size: drawSize, startX, startY, endX: e.offsetX, endY: e.offsetY });
                    redrawCanvas(canvas);
                });
            }

            function redrawCanvas(canvas) {
                const ctx = canvas.getContext("2d");
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                const canvasId = canvas.id;
                drawings[canvasId].forEach(({ tool, color, size, startX, startY, endX, endY }) => {
                    ctx.strokeStyle = color;
                    ctx.lineWidth = size;
                    ctx.beginPath();
                    ctx.moveTo(startX, startY);
                    ctx.lineTo(endX, endY);
                    ctx.stroke();
                });
            }

            function clearCanvas() {
                Object.keys(drawings).forEach(canvasId => {
                    drawings[canvasId] = [];
                    redrawCanvas(document.getElementById(canvasId));
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
                initializeCanvas(`${drawingNamespace}rightCanvas`);
                initializeCanvas(`${drawingNamespace}leftCanvas`);
            });

            return { setTool, setColor, setSize, clearCanvas };
        })();
    }

    window.CorneaDrawing = createDrawingManager("Cornea");
    window.PostSegDrawing = createDrawingManager("PostSeg");
</script>
