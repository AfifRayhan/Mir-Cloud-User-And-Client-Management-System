<x-app-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Send Email</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('mail.store') }}">
                            @csrf

                            <!-- Recipient -->
                            <div class="mb-3">
                                <label for="receiver_id" class="form-label fw-semibold">To</label>
                                <select id="receiver_id" 
                                        name="receiver_id" 
                                        class="form-select @error('receiver_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select recipient...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('receiver_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div class="mb-3">
                                <label for="subject" class="form-label fw-semibold">Subject</label>
                                <input id="subject" 
                                       class="form-control @error('subject') is-invalid @enderror" 
                                       type="text" 
                                       name="subject" 
                                       value="{{ old('subject') }}" 
                                       required 
                                       placeholder="Enter email subject">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Body -->
                            <div class="mb-4">
                                <label for="body" class="form-label fw-semibold">Message</label>
                                <textarea id="body" 
                                          class="form-control @error('body') is-invalid @enderror" 
                                          name="body" 
                                          rows="10" 
                                          required 
                                          placeholder="Enter your message...">{{ old('body') }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                        <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                                    </svg>
                                    Send Email
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
