<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="h4 mb-3">Candidate Application Form</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('application.store') }}" enctype="multipart/form-data" class="card shadow-sm">
            @csrf
            <div class="card-body">
                <div class="mb-4">
                    <h2 class="h6 mb-3">Select Post</h2>
                    <select name="post_id" class="form-select" required>
                        <option value="">Choose Post</option>
                        @foreach($posts as $post)
                        <option value="{{ $post->id }}" @selected(old('post_id', $selectedPostId) == $post->id)>
                                {{ $post->name ?? $post->title ?? $post->post_name ?? ('Post #'.$post->id) }}
                                @if(!empty($post->code)) ({{ $post->code }}) @endif
                                - Last Date: {{ $post->notification?->end_date?->format('d M Y') }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <h2 class="h6 mb-3">Personal Details</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" value="{{ old('category') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sub Reservation</label>
                            <input type="text" name="sub_reservation" value="{{ old('sub_reservation') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">DOB</label>
                            <input type="date" name="dob" value="{{ old('dob') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select</option>
                                <option value="male" @selected(old('gender') === 'male')>Male</option>
                                <option value="female" @selected(old('gender') === 'female')>Female</option>
                                <option value="other" @selected(old('gender') === 'other')>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Father Name</label>
                            <input type="text" name="father_name" value="{{ old('father_name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" value="{{ old('mobile') }}" class="form-control" maxlength="10" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-control" required>{{ old('address') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Aadhar Number (Optional)</label>
                            <input type="text" name="aadhar_number" value="{{ old('aadhar_number') }}" maxlength="12" class="form-control" placeholder="12 digits">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bank Account Number (Optional)</label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" maxlength="18" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IFSC Code (Optional)</label>
                            <input type="text" name="ifsc_code" value="{{ old('ifsc_code') }}" maxlength="11" class="form-control" style="text-transform: uppercase;">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Education Details</h2>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-education">Add Row</button>
                    </div>
                    <div id="education-wrapper">
                        <div class="row g-2 align-items-end education-row mb-2">
                            <div class="col-md-3"><input class="form-control" name="education[0][exam]" placeholder="Exam" required></div>
                            <div class="col-md-3"><input class="form-control" name="education[0][board_university]" placeholder="Board/University" required></div>
                            <div class="col-md-2"><input class="form-control" name="education[0][subject]" placeholder="Subject" required></div>
                            <div class="col-md-2"><input class="form-control" type="number" name="education[0][year]" placeholder="Year" required></div>
                            <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="education[0][percentage]" placeholder="%" required></div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Experience Details</h2>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-experience">Add Row</button>
                    </div>
                    <div id="experience-wrapper">
                        <div class="row g-2 align-items-end experience-row mb-2">
                            <div class="col-md-3"><input class="form-control" name="experience[0][organization]" placeholder="Organization"></div>
                            <div class="col-md-2"><input class="form-control" name="experience[0][post]" placeholder="Post"></div>
                            <div class="col-md-2"><input class="form-control" type="date" name="experience[0][from_date]"></div>
                            <div class="col-md-2"><input class="form-control" type="date" name="experience[0][to_date]"></div>
                            <div class="col-md-2"><input class="form-control" type="number" name="experience[0][total_months]" placeholder="Months"></div>
                            <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-row">X</button></div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="h6 mb-3">Demand Draft Details</h2>
                    <div class="row g-3">
                        <div class="col-md-3"><input class="form-control" name="dd_number" placeholder="DD Number" required></div>
                        <div class="col-md-3"><input class="form-control" name="bank_name" placeholder="Bank Name" required></div>
                        <div class="col-md-3"><input class="form-control" type="date" name="dd_date" required></div>
                        <div class="col-md-3"><input class="form-control" type="number" step="0.01" name="amount" placeholder="Amount" required></div>
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="h6 mb-3">Required Documents</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Photo (JPG/PNG)</label>
                            <input type="file" class="form-control" name="documents[photo]" accept=".jpg,.jpeg,.png" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Signature (JPG/PNG)</label>
                            <input type="file" class="form-control" name="documents[signature]" accept=".jpg,.jpeg,.png" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ID Proof (PDF/JPG/PNG)</label>
                            <input type="file" class="form-control" name="documents[id_proof]" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-end">
                <button class="btn btn-primary">Submit Application</button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            let educationIndex = 1;
            let experienceIndex = 1;

            const educationWrapper = document.getElementById('education-wrapper');
            const experienceWrapper = document.getElementById('experience-wrapper');

            document.getElementById('add-education').addEventListener('click', function () {
                const row = document.createElement('div');
                row.className = 'row g-2 align-items-end education-row mb-2';
                row.innerHTML = `
                    <div class="col-md-3"><input class="form-control" name="education[${educationIndex}][exam]" placeholder="Exam" required></div>
                    <div class="col-md-3"><input class="form-control" name="education[${educationIndex}][board_university]" placeholder="Board/University" required></div>
                    <div class="col-md-2"><input class="form-control" name="education[${educationIndex}][subject]" placeholder="Subject" required></div>
                    <div class="col-md-2"><input class="form-control" type="number" name="education[${educationIndex}][year]" placeholder="Year" required></div>
                    <div class="col-md-1"><input class="form-control" type="number" step="0.01" name="education[${educationIndex}][percentage]" placeholder="%"></div>
                    <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-row">X</button></div>
                `;
                educationWrapper.appendChild(row);
                educationIndex++;
            });

            document.getElementById('add-experience').addEventListener('click', function () {
                const row = document.createElement('div');
                row.className = 'row g-2 align-items-end experience-row mb-2';
                row.innerHTML = `
                    <div class="col-md-3"><input class="form-control" name="experience[${experienceIndex}][organization]" placeholder="Organization"></div>
                    <div class="col-md-2"><input class="form-control" name="experience[${experienceIndex}][post]" placeholder="Post"></div>
                    <div class="col-md-2"><input class="form-control" type="date" name="experience[${experienceIndex}][from_date]"></div>
                    <div class="col-md-2"><input class="form-control" type="date" name="experience[${experienceIndex}][to_date]"></div>
                    <div class="col-md-2"><input class="form-control" type="number" name="experience[${experienceIndex}][total_months]" placeholder="Months"></div>
                    <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-row">X</button></div>
                `;
                experienceWrapper.appendChild(row);
                experienceIndex++;
            });

            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-row')) {
                    event.target.closest('.row')?.remove();
                }
            });
        })();
    </script>
</body>
</html>
