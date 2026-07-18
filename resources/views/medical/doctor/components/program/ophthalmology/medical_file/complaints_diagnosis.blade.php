<div class="tab-pane active" id="medical_history" role="tabpanel">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Complaint</h6>
                        </div>
                        <div class="flex-shrink-0">
                            <ul class="list-inline card-toolbar-menu d-flex align-items-center mb-0">
                                <li class="list-inline-item">
                                    <a class="align-middle minimize-card" data-bs-toggle="collapse"
                                        href="#collapseExample3" role="button" aria-expanded="true"
                                        aria-controls="collapseExample3">
                                        <i class="mdi mdi-minus align-middle"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body collapse show" id="collapseExample3">
                    <!-- Complaint Form -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="complaint_category" class="form-label">Complaint Category</label>
                            <input type="text" class="form-control" id="complaint_category"
                                placeholder="Complaint Category">
                            <div id="categorySuggestions" class="list-group position-absolute w-100"
                                style="z-index: 1050; height: 200px; overflow: auto; display: none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Eye</label>
                            <div class="btn-group w-100 eye-selection-group" role="group">
                                <input type="radio" class="btn-check" name="eye" id="eye_od" value="OD">
                                <label class="btn btn-outline-primary text-center" for="eye_od"
                                    style="font-size:18px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 5px 0;">
                                    <i class="mdi mdi-eye" style="font-size: 18px;"></i>
                                    OD
                                </label>

                                <input type="radio" class="btn-check" name="eye" id="eye_ou" value="OU">
                                <label class="btn btn-outline-primary text-center" for="eye_ou"
                                    style="font-size: 18px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 5px 0;">
                                    <div style="display: flex; gap: 4px; justify-content: center;">
                                        <i class="mdi mdi-eye" style="font-size: 18px;"></i>
                                        <i class="mdi mdi-eye" style="font-size: 18px;"></i>
                                    </div>
                                    <span>OU</span>
                                </label>

                                <input type="radio" class="btn-check" name="eye" id="eye_os" value="OS">
                                <label class="btn btn-outline-primary text-center" for="eye_os"
                                    style="font-size: 18px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 5px 0;">
                                    <i class="mdi mdi-eye" style="font-size: 18px;"></i>
                                    OS
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="onset" class="form-label">Onset</label>
                            <input type="text" class="form-control" id="onset" placeholder="Start typing...">
                            <div id="onsetSuggestions" class="list-group position-absolute w-100"
                                style="z-index: 1000; display: none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="duration" class="form-label">For</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="duration">
                                <select class="form-select" id="duration_unit">
                                    <option value="D">Days</option>
                                    <option value="W">Weeks</option>
                                    <option value="M">Months</option>
                                    <option value="Y">Years</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="course" class="form-label">Course</label>
                            <input type="text" class="form-control" id="course" placeholder="Start typing...">
                            <div id="courseSuggestions" class="list-group position-absolute w-100"
                                style="z-index: 1000; display: none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" rows="3" placeholder="Enter remarks"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-success" onclick="addComplaint()">Add Complaint</button>
                        <button type="button" class="btn btn-danger" onclick="clearForm()">Clear Form</button>
                    </div>
                    <hr>
                    <!-- Complaints List -->
                    <div id="complaints_list">
                        <h5>Complaints List</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Complaint</th>
                                        <th>Eye</th>
                                        <th>Onset</th>
                                        <th>Duration</th>
                                        <th>Course</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="complaints_table_body">
                                    <!-- Rows will be dynamically added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let complaints = [];
    let editingIndex = null; // To track the index of the complaint being edited

    function addComplaint() {
        const complaint = {
            category: document.getElementById('complaint_category').value,
            eye: document.querySelector('input[name="eye"]:checked')?.value || '',
            onset: document.getElementById('onset').value,
            duration: document.getElementById('duration').value + ' ' + document.getElementById('duration_unit').value,
            course: document.getElementById('course').value,
            remarks: document.getElementById('remarks').value
        };

        if (!complaint.category || !complaint.eye) {
            alert("Please fill all required fields.");
            return;
        }

        if (editingIndex !== null) {
            // Update existing complaint
            complaints[editingIndex] = complaint;
            editingIndex = null; // Reset editing index
        } else {
            // Add new complaint
            complaints.push(complaint);
        }

        // Send data to the server
        saveComplaintToDatabase(complaint);

        renderComplaints();
        clearForm();
    }

    function saveComplaintToDatabase(complaint) {
        $.ajax({
            url: "{{ route('save.complaint') }}", // Replace with your route
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}", // CSRF token for Laravel
                complaint: complaint
            },
            success: function (response) {
                if (response.success) {
                    alert("Complaint saved successfully!");
                } else {
                    alert("Failed to save complaint.");
                }
            },
            error: function (xhr) {
                alert("An error occurred while saving the complaint.");
            }
        });
    }

    function deleteComplaint(index) {
        complaints.splice(index, 1);
        renderComplaints();
    }

    function editComplaint(index) {
        const complaint = complaints[index];
        document.getElementById('complaint_category').value = complaint.category;
        document.querySelector(`input[name="eye"][value="${complaint.eye}"]`).checked = true;
        document.getElementById('onset').value = complaint.onset;
        const [durationValue, durationUnit] = complaint.duration.split(' ');
        document.getElementById('duration').value = durationValue;
        document.getElementById('duration_unit').value = durationUnit;
        document.getElementById('course').value = complaint.course;
        document.getElementById('remarks').value = complaint.remarks;

        editingIndex = index; // Set the editing index
    }

    function renderComplaints() {
        const tbody = document.getElementById('complaints_table_body');
        tbody.innerHTML = '';
        complaints.forEach((c, index) => {
            const row = `
                <tr>
                    <td><strong>${c.category}</strong><br></td>
                    <td>${c.eye}</td>
                    <td><strong>${c.onset}</strong><br></td>
                    <td>${c.duration}</td>
                    <td><strong>${c.course}</strong><br></td>
                    <td>${c.remarks}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editComplaint(${index})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteComplaint(${index})">Delete</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function clearForm() {
        document.getElementById('complaint_category').value = '';
        document.querySelectorAll('input[name="eye"]').forEach(el => el.checked = false);
        document.getElementById('onset').value = '';
        document.getElementById('duration').value = '';
        document.getElementById('duration_unit').value = 'D';
        document.getElementById('course').value = '';
        document.getElementById('remarks').value = '';
        editingIndex = null; // Reset editing index
    }

    $(document).ready(function () {
        // Autocomplete for Complaint Category
        setupAutocomplete('#complaint_category', '#categorySuggestions', "{{ route('autocomplete.complaint_categories') }}");

        // Autocomplete for Onset
        setupAutocomplete('#onset', '#onsetSuggestions', [
            "Sudden", "Gradual", "Accidental discovery", "Since birth", "Since Childhood", "Unknown"
        ]);

        // Autocomplete for Course
        setupAutocomplete('#course', '#courseSuggestions', [
            "Progressive", "Regressive", "Stationary", "Remission exacerbation", "Recurrent"
        ]);
    });

    function setupAutocomplete(inputId, suggestionsId, optionsOrUrl) {
        let timer = null;
        let suggestionsBox = $(suggestionsId);

        $(inputId).on('input', function () {
            clearTimeout(timer);
            let query = $(this).val();

            if (query.length > 0) {
                timer = setTimeout(function () {
                    if (typeof optionsOrUrl === 'string') {
                        // Fetch data from the server using AJAX
                        $.ajax({
                            url: optionsOrUrl,
                            type: 'GET',
                            data: { query: query },
                            success: function (data) {
                                suggestionsBox.empty();
                                if (data.length > 0) {
                                    data.forEach(function (item) {
                                        suggestionsBox.append(
                                            `<a href="#" class="list-group-item list-group-item-action">${item}</a>`
                                        );
                                    });
                                    suggestionsBox.show();
                                } else {
                                    suggestionsBox.hide();
                                }
                            }
                        });
                    } else {
                        // Use local options
                        let filteredOptions = optionsOrUrl.filter(function (option) {
                            return option.toLowerCase().includes(query.toLowerCase());
                        });

                        suggestionsBox.empty();
                        if (filteredOptions.length > 0) {
                            filteredOptions.forEach(function (item) {
                                suggestionsBox.append(
                                    `<a href="#" class="list-group-item list-group-item-action">${item}</a>`
                                );
                            });
                            suggestionsBox.show();
                        } else {
                            suggestionsBox.hide();
                        }
                    }
                }, 300);
            } else {
                suggestionsBox.hide();
            }
        });

        // Double click to show all options
        $(inputId).on('dblclick', function () {
            if (typeof optionsOrUrl === 'string') {
                // Fetch all data from the server using AJAX
                $.ajax({
                    url: optionsOrUrl,
                    type: 'GET',
                    data: { query: '' }, // Empty query to fetch all
                    success: function (data) {
                        suggestionsBox.empty();
                        if (data.length > 0) {
                            data.forEach(function (item) {
                                suggestionsBox.append(
                                    `<a href="#" class="list-group-item list-group-item-action">${item}</a>`
                                );
                            });
                            suggestionsBox.show();
                        } else {
                            suggestionsBox.hide();
                        }
                    }
                });
            } else {
                // Use local options
                suggestionsBox.empty();
                optionsOrUrl.forEach(function (item) {
                    suggestionsBox.append(
                        `<a href="#" class="list-group-item list-group-item-action">${item}</a>`
                    );
                });
                suggestionsBox.show();
            }
        });

        suggestionsBox.on('click', 'a', function (e) {
            e.preventDefault();
            $(inputId).val($(this).text());
            suggestionsBox.hide();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest(inputId + ', ' + suggestionsId).length) {
                suggestionsBox.hide();
            }
        });
    }
</script>