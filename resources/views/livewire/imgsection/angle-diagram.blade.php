<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white text-center">
        <h5 class="mb-0"  style="color: #fff">Ophthalmology Documentation - Angle Examination</h5>
    </div>
    <div class="card-body">
        <div class="row">
             <!-- ✅ الأدوات -->
            <div class="col-md-12 text-center mb-3">
                <button class="btn btn-outline-secondary" onclick="copyToLeft()">⬅️ Copy to Left</button>
                <button class="btn btn-outline-secondary" onclick="copyToRight()">➡️ Copy to Right</button>
                <button class="btn btn-outline-success" onclick="saveImage()">💾 Save Image</button>
            </div>
            <!-- ✅ العين اليمنى -->
            <div class="col-md-6 text-center">
                <h6 class="text-primary">Right Eye - Angle</h6>
                <div class="position-relative d-inline-block" id="rightAngleContainer" style="width: 650px;">
                    <img src="{{ asset('assets/images/OPTH/angle_rt.png') }}" class="img-fluid rounded shadow-sm"
                        alt="Angle Right Eye" style="height: 351px;width: 647px;">

                    <!-- ✅ القوائم المنسدلة والتعليقات حول الدائرة -->
                    <div class="angle-group" style="top: 13%; left: 53%;">
                         <div class="dropdown-group">
                            <select class="dropdown form-select">
                                <option>Shaffer</option>
                                <option>VI (Wide Open)</option>
                                <option>III</option>
                                <option>II</option>
                                <option>I</option>
                                <option>Slit</option>
                                <option>Occludable</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Pigmentation</option>
                                <option>+1</option>
                                <option>+2</option>
                                <option>+3</option>
                                <option>+4</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Others</option>
                                <option>Bl vs</option>
                                <option>Recession</option>
                                <option>PAS</option>
                                <option>Tube Open</option>
                                <option>Tube Closes</option>
                                <option>PI Open</option>
                                <option>PI Closed</option>
                                <option>MIGS</option>
                                <option>Mass</option>
                            </select>
                        </div>
                        <input type="text" class="comment-box" placeholder="Comment">
                    </div>

                    <div class="angle-group" style="bottom: -4%; left: 53%;">
                        <div class="dropdown-group">
                            <select class="dropdown form-select">
                                <option>Shaffer</option>
                                <option>VI (Wide Open)</option>
                                <option>III</option>
                                <option>II</option>
                                <option>I</option>
                                <option>Slit</option>
                                <option>Occludable</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Pigmentation</option>
                                <option>+1</option>
                                <option>+2</option>
                                <option>+3</option>
                                <option>+4</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Others</option>
                                <option>Bl vs</option>
                                <option>Recession</option>
                                <option>PAS</option>
                                <option>Tube Open</option>
                                <option>Tube Closes</option>
                                <option>PI Open</option>
                                <option>PI Closed</option>
                                <option>MIGS</option>
                                <option>Mass</option>
                            </select>
                        </div>
                        <input type="text" class="comment-box" placeholder="Comment">
                    </div>

                    <div class="angle-group" style="top: 52%; left: 20%;">
                        <div class="dropdown-group">
                            <select class="dropdown form-select">
                                <option>Shaffer</option>
                                <option>VI (Wide Open)</option>
                                <option>III</option>
                                <option>II</option>
                                <option>I</option>
                                <option>Slit</option>
                                <option>Occludable</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Pigmentation</option>
                                <option>+1</option>
                                <option>+2</option>
                                <option>+3</option>
                                <option>+4</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Others</option>
                                <option>Bl vs</option>
                                <option>Recession</option>
                                <option>PAS</option>
                                <option>Tube Open</option>
                                <option>Tube Closes</option>
                                <option>PI Open</option>
                                <option>PI Closed</option>
                                <option>MIGS</option>
                                <option>Mass</option>
                            </select>
                        </div>
                        <input type="text" class="comment-box" placeholder="Comment">
                    </div>

                    <div class="angle-group" style="top: 53%; right: -20%;">
                         <div class="dropdown-group">
                            <select class="dropdown form-select">
                                <option>Shaffer</option>
                                <option>VI (Wide Open)</option>
                                <option>III</option>
                                <option>II</option>
                                <option>I</option>
                                <option>Slit</option>
                                <option>Occludable</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Pigmentation</option>
                                <option>+1</option>
                                <option>+2</option>
                                <option>+3</option>
                                <option>+4</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Others</option>
                                <option>Bl vs</option>
                                <option>Recession</option>
                                <option>PAS</option>
                                <option>Tube Open</option>
                                <option>Tube Closes</option>
                                <option>PI Open</option>
                                <option>PI Closed</option>
                                <option>MIGS</option>
                                <option>Mass</option>
                            </select>
                        </div>
                        <input type="text" class="comment-box" placeholder="Comment">
                    </div>
                </div>
            </div>

            <!-- ✅ العين اليسرى -->
            <div class="col-md-6 text-center">
                <h6 class="text-primary">Left Eye - Angle</h6>
                <div class="position-relative d-inline-block" id="leftAngleContainer" style="width: 650px;">
                    <img src="{{ asset('assets/images/OPTH/angle_lt.png') }}" class="img-fluid rounded shadow-sm"
                        alt="Angle Left Eye" style="height: 351px;width: 647px;">

                    <!-- ✅ القوائم المنسدلة والتعليقات حول الدائرة -->
                    <div class="angle-group" style="top: 17%; left: 50%;">
                        <div class="dropdown-group">
                            <select class="dropdown form-select">
                                <option>Shaffer</option>
                                <option>VI (Wide Open)</option>
                                <option>III</option>
                                <option>II</option>
                                <option>I</option>
                                <option>Slit</option>
                                <option>Occludable</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Pigmentation</option>
                                <option>+1</option>
                                <option>+2</option>
                                <option>+3</option>
                                <option>+4</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Others</option>
                                <option>Bl vs</option>
                                <option>Recession</option>
                                <option>PAS</option>
                                <option>Tube Open</option>
                                <option>Tube Closes</option>
                                <option>PI Open</option>
                                <option>PI Closed</option>
                                <option>MIGS</option>
                                <option>Mass</option>
                            </select>
                        </div>
                        <input type="text" class="comment-box" placeholder="Comment">
                    </div>

                    <div class="angle-group" style="bottom: 5%; left: 50%;">
                       <div class="dropdown-group">
                            <select class="dropdown form-select">
                                <option>Shaffer</option>
                                <option>VI (Wide Open)</option>
                                <option>III</option>
                                <option>II</option>
                                <option>I</option>
                                <option>Slit</option>
                                <option>Occludable</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Pigmentation</option>
                                <option>+1</option>
                                <option>+2</option>
                                <option>+3</option>
                                <option>+4</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Others</option>
                                <option>Bl vs</option>
                                <option>Recession</option>
                                <option>PAS</option>
                                <option>Tube Open</option>
                                <option>Tube Closes</option>
                                <option>PI Open</option>
                                <option>PI Closed</option>
                                <option>MIGS</option>
                                <option>Mass</option>
                            </select>
                        </div>
                        <input type="text" class="comment-box" placeholder="Comment">
                    </div>

                    <div class="angle-group" style="top: 50%; left: 18%;">
                         <div class="dropdown-group">
                            <select class="dropdown form-select">
                                <option>Shaffer</option>
                                <option>VI (Wide Open)</option>
                                <option>III</option>
                                <option>II</option>
                                <option>I</option>
                                <option>Slit</option>
                                <option>Occludable</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Pigmentation</option>
                                <option>+1</option>
                                <option>+2</option>
                                <option>+3</option>
                                <option>+4</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Others</option>
                                <option>Bl vs</option>
                                <option>Recession</option>
                                <option>PAS</option>
                                <option>Tube Open</option>
                                <option>Tube Closes</option>
                                <option>PI Open</option>
                                <option>PI Closed</option>
                                <option>MIGS</option>
                                <option>Mass</option>
                            </select>
                        </div>
                        <input type="text" class="comment-box" placeholder="Comment">
                    </div>

                    <div class="angle-group" style="top: 50%; right: -21%;">
                        <div class="dropdown-group">
                            <select class="dropdown form-select">
                                <option>Shaffer</option>
                                <option>VI (Wide Open)</option>
                                <option>III</option>
                                <option>II</option>
                                <option>I</option>
                                <option>Slit</option>
                                <option>Occludable</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Pigmentation</option>
                                <option>+1</option>
                                <option>+2</option>
                                <option>+3</option>
                                <option>+4</option>
                            </select>
                            <select class="dropdown form-select">
                                <option>Others</option>
                                <option>Bl vs</option>
                                <option>Recession</option>
                                <option>PAS</option>
                                <option>Tube Open</option>
                                <option>Tube Closes</option>
                                <option>PI Open</option>
                                <option>PI Closed</option>
                                <option>MIGS</option>
                                <option>Mass</option>
                            </select>
                        </div>
                        <input type="text" class="comment-box" placeholder="Comment">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ✅ تحسين CSS -->
<style>
    .angle-group {
        position: absolute;
        width: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transform: translate(-50%, -50%);
    }

    .dropdown-group {
        display: flex;
        gap: 5px;
    }

    .dropdown {
        width: 80px;
        height: 30px;
    }

    .comment-box {
        width: 200px;
        height: 25px;
        text-align: center;
        margin-top: 5px;
    }

    .selected-value {
        margin-top: 5px;
        font-weight: bold;
        color: #007bff;
    }
</style>

<!-- ✅ سكريبت جافاسكريبت -->
<script>
    function displaySelected(selectElement) {
        const parentDiv = selectElement.closest('.angle-group');
        const displaySpan = parentDiv.querySelector('.selected-value');
        displaySpan.textContent = selectElement.options[selectElement.selectedIndex].text;
    }

    function clearSelection(button) {
        const parentDiv = button.closest('.angle-group');
        const dropdowns = parentDiv.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => dropdown.selectedIndex = 0);
        parentDiv.querySelector('.selected-value').textContent = "";
    }

    function copyToLeft() {
        document.querySelectorAll("#rightAngleContainer .angle-group").forEach((group, index) => {
            let leftGroup = document.querySelectorAll("#leftAngleContainer .angle-group")[index];
            let rightDropdowns = group.querySelectorAll(".dropdown");
            let leftDropdowns = leftGroup.querySelectorAll(".dropdown");
            
            rightDropdowns.forEach((dropdown, i) => {
                leftDropdowns[i].selectedIndex = dropdown.selectedIndex;
            });

            leftGroup.querySelector(".comment-box").value = group.querySelector(".comment-box").value;
            leftGroup.querySelector(".selected-value").textContent = group.querySelector(".selected-value").textContent;
        });
    }

    function copyToRight() {
        document.querySelectorAll("#leftAngleContainer .angle-group").forEach((group, index) => {
            let rightGroup = document.querySelectorAll("#rightAngleContainer .angle-group")[index];
            let leftDropdowns = group.querySelectorAll(".dropdown");
            let rightDropdowns = rightGroup.querySelectorAll(".dropdown");

            leftDropdowns.forEach((dropdown, i) => {
                rightDropdowns[i].selectedIndex = dropdown.selectedIndex;
            });

            rightGroup.querySelector(".comment-box").value = group.querySelector(".comment-box").value;
            rightGroup.querySelector(".selected-value").textContent = group.querySelector(".selected-value").textContent;
        });
    }

function saveImage() {
    const rightContainer = document.getElementById("rightAngleContainer");
    const leftContainer = document.getElementById("leftAngleContainer");
    const rightImg = rightContainer.querySelector("img");
    const leftImg = leftContainer.querySelector("img");

    const canvas = document.createElement("canvas");
    canvas.width = rightImg.width * 2 + 20;
    canvas.height = rightImg.height + 80;
    const ctx = canvas.getContext("2d");

    const tempRightImg = new Image();
    tempRightImg.src = rightImg.src;

    const tempLeftImg = new Image();
    tempLeftImg.src = leftImg.src;

    tempRightImg.onload = function () {
        tempLeftImg.onload = function () {
            ctx.drawImage(leftImg, 0, 0, leftImg.width, leftImg.height);
            ctx.drawImage(rightImg, leftImg.width + 20, 0, rightImg.width, rightImg.height);

            addTextToCanvas(ctx, leftContainer, 10, leftImg.height + 20);
            addTextToCanvas(ctx, rightContainer, leftImg.width + 30, rightImg.height + 20);

            canvas.toBlob((blob) => {
                let formData = new FormData();
                formData.append("image", blob);
                formData.append("_token", document.querySelector("meta[name='csrf-token']").getAttribute("content"));

                fetch("/save-angle-image", {
                    method: "POST",
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          alert("Image saved successfully!");
                      } else {
                          alert("Error saving image.");
                      }
                  }).catch(error => console.error("Error:", error));
            }, "image/png");
        };
    };
}


</script>
