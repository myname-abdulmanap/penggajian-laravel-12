@extends('layouts.app')

@section('content')
    <style>
        .profile-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .profile-header {

            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            margin: 0 auto 1rem;
            display: block;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .default-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border: 5px solid white;
        }

        .profile-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .profile-role {
            font-size: 1rem;
            opacity: 0.9;
            text-transform: capitalize;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .info-item:hover {
            background-color: #f8f9fa;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 20px;
            color: #667eea;
            margin-right: 1rem;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 100px;
        }

        .info-value {
            color: #333;
            flex: 1;
        }

        /* Button Styles - Purple for Edit Profile */
        .btn-edit {

            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Green Theme for Form Actions */
        .btn-success-custom {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: white;
            background: linear-gradient(135deg, #218838 0%, #1fa794 100%);
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }

        .form-label i {
            color: #28a745;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }

        .photo-preview {
            max-width: 150px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .edit-form-header {
            color: #28a745;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .btn-outline-secondary {
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
    </style>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Profile Card -->
                <div class="profile-card fade-in">
                    <!-- Profile Header -->
                    <div class="profile-header bg-success text-white">
                        @if ($user->photo)
                            <img src="{{ Storage::url($user->photo) }}" alt="Profile Photo" class="profile-avatar">
                        @else
                            <div class="default-avatar">
                                <i class="fas fa-user fa-3x" style="color: white; opacity: 0.7;"></i>
                            </div>
                        @endif
                        <h2 class="profile-name">{{ $user->name }}</h2>
                        <p class="profile-role">
                            <i class="fas fa-user-tag me-1"></i>
                            {{ ucfirst($user->role ?? 'Karyawan') }}
                        </p>
                    </div>

                    <!-- Profile Details -->
                    <div id="profile-detail">
                        <div class="p-4">
                            <div class="info-item">
                                <i class="fas fa-envelope info-icon"></i>
                                <span class="info-label">Email:</span>
                                <span class="info-value">{{ $user->email }}</span>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-phone info-icon"></i>
                                <span class="info-label">Telepon:</span>
                                <span class="info-value">{{ $user->phone ?? 'Belum diisi' }}</span>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-briefcase info-icon"></i>
                                <span class="info-label">Jabatan:</span>
                                <span class="info-value">{{ $user->job_title ?? 'Belum diisi' }}</span>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-map-marker-alt info-icon"></i>
                                <span class="info-label">Alamat:</span>
                                <span class="info-value">{{ $user->address ?? 'Belum diisi' }}</span>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-circle info-icon"></i>
                                <span class="info-label">Status:</span>
                                <span class="info-value">
                                    <span class="badge bg-{{ $user->status == 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($user->status ?? 'Aktif') }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="text-center p-4 border-top">
                            <button class="btn btn-edit bg-success" onclick="toggleEdit()">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </button>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div id="edit-form" style="display: none;" class="fade-in">
                        <div class="p-4 border-top">
                            <h4 class="edit-form-header">
                                <i class="fas fa-user-edit me-2"> </i> Edit Profile
                            </h4>

                            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">
                                                <i class="fas fa-user me-1"></i> Nama Lengkap
                                            </label>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">
                                                <i class="fas fa-envelope me-1"></i> Email
                                            </label>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">
                                                <i class="fas fa-phone me-1"></i> Nomor Telepon
                                            </label>
                                            <input type="text" id="phone" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="job_title" class="form-label">
                                                <i class="fas fa-briefcase me-1"></i> Jabatan
                                            </label>
                                            <input type="text" id="job_title" name="job_title"
                                                class="form-control @error('job_title') is-invalid @enderror"
                                                value="{{ old('job_title', $user->job_title) }}"
                                                placeholder="Belum diisi" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="photo" class="form-label">
                                                <i class="fas fa-camera me-1"></i> Foto Profile
                                            </label>
                                            @if ($user->photo)
                                                <div class="mb-3">
                                                    <small class="text-muted d-block mb-2">Foto saat ini:</small>
                                                    <img src="{{ Storage::url($user->photo) }}" alt="Current Photo"
                                                        class="photo-preview">
                                                </div>
                                            @endif
                                            <input type="file" id="photo" name="photo"
                                                class="form-control @error('photo') is-invalid @enderror"
                                                accept="image/jpeg,image/png,image/jpg,image/gif">
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Format yang didukung: JPEG, PNG, JPG, GIF. Maksimal 2MB.
                                            </div>
                                            @error('photo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="address" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i> Alamat Lengkap
                                    </label>
                                    <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                                        placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleEdit()">
                                        <i class="fas fa-times me-2"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-success-custom">
                                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEdit() {
            const detail = document.getElementById('profile-detail');
            const form = document.getElementById('edit-form');

            if (detail.style.display === 'none') {
                detail.style.display = 'block';
                form.style.display = 'none';
            } else {
                detail.style.display = 'none';
                form.style.display = 'block';
                form.classList.add('fade-in');
            }
        }

        // Auto dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-success')) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);

        // Preview foto sebelum upload
        document.getElementById('photo').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(result) {
                    // Create preview element if doesn't exist
                    let preview = document.getElementById('photo-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'photo-preview';
                        preview.className = 'photo-preview mt-2';
                        e.target.parentNode.appendChild(preview);
                    }
                    preview.src = result.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
@endsection
