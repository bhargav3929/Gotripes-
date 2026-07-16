@include('header')

<main class="umrah-visa-page" style="background: #000; min-height: 100vh; color: #fff; font-family: 'Outfit', sans-serif; padding-bottom: 60px;">
    <!-- Hero Section -->
    <section class="umrah-hero" style="position: relative; height: 45vh; min-height: 300px; background: url('{{ asset('assets/index_files/umrah_1.png') }}') center center / cover no-repeat; display: flex; align-items: center; justify-content: center; text-align: center;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.65);"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <h1 style="font-size: clamp(28px, 6vw, 54px); font-weight: 800; letter-spacing: 2px; background: linear-gradient(135deg, #FFD700 0%, #D4AF37 50%, #B8960C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin-bottom: 15px;">Umrah & Saudi Visas</h1>
            <p style="font-size: clamp(14px, 2vw, 18px); color: #ccc; max-width: 700px; margin: 0 auto; line-height: 1.6;">Experience a sacred pilgrimage of a lifetime with our admin-driven, high-quality Hajj & Umrah packages and Saudi Visa services.</p>
        </div>
    </section>

    <!-- Tab Navigation -->
    <section class="tab-section" style="background: #050505; border-bottom: 1px solid rgba(255,255,255,0.08); position: sticky; top: 0; z-index: 100; padding: 0;">
        <div class="container">
            <div class="premium-nav-tabs">
                <button type="button" class="tab-btn active" onclick="switchTab('bus-tab')">
                    <i class="bi bi-bus-front-fill"></i> Umrah by Bus
                </button>
                <button type="button" class="tab-btn" onclick="switchTab('air-tab')">
                    <i class="bi bi-airplane-engines-fill"></i> Umrah by Air
                </button>
                <button type="button" class="tab-btn" onclick="switchTab('visa-tab')">
                    <i class="bi bi-file-earmark-person-fill"></i> Saudi Visas
                </button>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <div class="container mt-5">
        
        <!-- 🚌 Umrah by Bus Section -->
        <section id="bus-tab" class="tab-content active">
            <!-- Filter Categories -->
            <div class="d-flex flex-wrap justify-content-center mb-4" style="gap: 10px;">
                <button type="button" class="filter-btn active" onclick="filterCategory('all')">All Packages</button>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <button type="button" class="filter-btn" onclick="filterCategory('{{ strtolower(str_replace(' ', '-', $cat->name)) }}')">{{ $cat->name }}</button>
                    @endforeach
                @else
                    <button type="button" class="filter-btn" onclick="filterCategory('economy')">Economy</button>
                    <button type="button" class="filter-btn" onclick="filterCategory('standard')">Standard</button>
                    <button type="button" class="filter-btn" onclick="filterCategory('premium')">Premium</button>
                    <button type="button" class="filter-btn" onclick="filterCategory('vip')">VIP</button>
                @endif
            </div>

            @if(isset($packagesBus) && $packagesBus->count() > 0)
            <div class="row g-4">
                @foreach($packagesBus as $pkg)
                <div class="col-md-6 col-lg-4 package-item" data-category="{{ $pkg->umrahCategory ? strtolower(str_replace(' ', '-', $pkg->umrahCategory->name)) : 'uncategorized' }}">
                    <div class="package-card" style="background: #0b0b0b; border: 1px solid #1a1a1a; border-radius: 18px; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: all 0.3s ease;">
                        <div class="package-img-wrap" style="position: relative; height: 220px; background: url('{{ asset($pkg->image) }}') center center / cover no-repeat;">
                            <span class="category-badge" style="position: absolute; top: 15px; left: 15px; background: #FFD700; color: #000; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 50px; text-transform: uppercase;">
                                {{ $pkg->umrahCategory ? $pkg->umrahCategory->name : 'Package' }}
                            </span>
                            @if($pkg->tag)
                            <span class="tag-badge" style="position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.7); color: #FFD700; border: 1px solid #FFD700; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 50px;">
                                {{ $pkg->tag }}
                            </span>
                            @endif
                        </div>
                        <div class="package-body" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 10px; color: #fff;">{{ $pkg->title }}</h3>
                                <p style="color: #888; font-size: 14px; margin-bottom: 20px; line-height: 1.5;">{{ Str::limit($pkg->description, 120) }}</p>
                                
                                <div class="pkg-meta mb-3" style="font-size: 13px; color: #ccc;">
                                    <div class="mb-2"><i class="bi bi-clock-history text-warning me-2"></i><strong>Duration:</strong> {{ $pkg->duration }}</div>
                                    @if($pkg->hotels)
                                    <div class="mb-2"><i class="bi bi-building text-warning me-2"></i><strong>Hotels:</strong> {{ Str::limit($pkg->hotels, 40) }}</div>
                                    @endif
                                    @if($pkg->transport)
                                    <div class="mb-2"><i class="bi bi-bus-front text-warning me-2"></i><strong>Transport:</strong> {{ Str::limit($pkg->transport, 40) }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div style="border-top: 1px solid #1a1a1a; padding-top: 20px; margin-top: auto; display: flex; align-items: center; justify-content: space-between;">
                                <div class="price-box">
                                    <span style="font-size: 12px; color: #888; display: block;">Starting From</span>
                                    @if($pkg->discount_price && $pkg->discount_price < $pkg->original_price)
                                        <span style="text-decoration: line-through; font-size: 13px; color: #888; margin-right: 4px;">AED {{ number_format($pkg->original_price, 0) }}</span>
                                        <strong style="font-size: 20px; color: #FFD700;">AED {{ number_format($pkg->starting_price, 0) }}</strong>
                                    @else
                                        <strong style="font-size: 20px; color: #FFD700;">AED {{ number_format($pkg->starting_price, 0) }}</strong>
                                    @endif
                                </div>
                                <a href="{{ route('umrah-visas.show', $pkg->id) }}" class="btn-book-now" style="background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%); color: #000; font-weight: 700; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; transition: all 0.2s;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-calendar2-x" style="font-size: 48px; color: #FFD700;"></i>
                <h4 class="mt-3">No Hajj & Umrah Packages Available</h4>
                <p style="color: #666;">Please check back later or contact us for custom packages.</p>
            </div>
            @endif
        </section>

        <!-- ✈️ Umrah by Air Section -->
        <section id="air-tab" class="tab-content d-none">
            <!-- Filter Categories -->
            <div class="d-flex flex-wrap justify-content-center mb-4" style="gap: 10px;">
                <button type="button" class="filter-btn active" onclick="filterCategory('all', 'air')">All Packages</button>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <button type="button" class="filter-btn" onclick="filterCategory('{{ strtolower(str_replace(' ', '-', $cat->name)) }}', 'air')">{{ $cat->name }}</button>
                    @endforeach
                @else
                    <button type="button" class="filter-btn" onclick="filterCategory('economy', 'air')">Economy</button>
                    <button type="button" class="filter-btn" onclick="filterCategory('standard', 'air')">Standard</button>
                    <button type="button" class="filter-btn" onclick="filterCategory('premium', 'air')">Premium</button>
                    <button type="button" class="filter-btn" onclick="filterCategory('vip', 'air')">VIP</button>
                @endif
            </div>

            @if(isset($packagesAir) && $packagesAir->count() > 0)
            <div class="row g-4" id="air-packages-container">
                @foreach($packagesAir as $pkg)
                <div class="col-md-6 col-lg-4 package-item-air" data-category="{{ $pkg->umrahCategory ? strtolower(str_replace(' ', '-', $pkg->umrahCategory->name)) : 'uncategorized' }}">
                    <div class="package-card" style="background: #0b0b0b; border: 1px solid #1a1a1a; border-radius: 18px; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: all 0.3s ease;">
                        <div class="package-img-wrap" style="position: relative; height: 220px; background: url('{{ asset($pkg->image) }}') center center / cover no-repeat;">
                            <span class="category-badge" style="position: absolute; top: 15px; left: 15px; background: #FFD700; color: #000; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 50px; text-transform: uppercase;">
                                {{ $pkg->umrahCategory ? $pkg->umrahCategory->name : 'Package' }}
                            </span>
                            @if($pkg->tag)
                            <span class="tag-badge" style="position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.7); color: #FFD700; border: 1px solid #FFD700; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 50px;">
                                {{ $pkg->tag }}
                            </span>
                            @endif
                        </div>
                        <div class="package-body" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 10px; color: #fff;">{{ $pkg->title }}</h3>
                                <p style="color: #888; font-size: 14px; margin-bottom: 20px; line-height: 1.5;">{{ Str::limit($pkg->description, 120) }}</p>
                                
                                <div class="pkg-meta mb-3" style="font-size: 13px; color: #ccc;">
                                    <div class="mb-2"><i class="bi bi-clock-history text-warning me-2"></i><strong>Duration:</strong> {{ $pkg->duration }}</div>
                                    @if($pkg->airline)
                                    <div class="mb-2"><i class="bi bi-airplane text-warning me-2"></i><strong>Airline:</strong> {{ $pkg->airline }}</div>
                                    @endif
                                    @if($pkg->hotels)
                                    <div class="mb-2"><i class="bi bi-building text-warning me-2"></i><strong>Hotels:</strong> {{ Str::limit($pkg->hotels, 40) }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div style="border-top: 1px solid #1a1a1a; padding-top: 20px; margin-top: auto; display: flex; align-items: center; justify-content: space-between;">
                                <div class="price-box">
                                    <span style="font-size: 12px; color: #888; display: block;">Starting From</span>
                                    @if($pkg->discount_price && $pkg->discount_price < $pkg->original_price)
                                        <span style="text-decoration: line-through; font-size: 13px; color: #888; margin-right: 4px;">AED {{ number_format($pkg->original_price, 0) }}</span>
                                        <strong style="font-size: 20px; color: #FFD700;">AED {{ number_format($pkg->starting_price, 0) }}</strong>
                                    @else
                                        <strong style="font-size: 20px; color: #FFD700;">AED {{ number_format($pkg->starting_price, 0) }}</strong>
                                    @endif
                                </div>
                                <a href="{{ route('umrah-visas.show', $pkg->id) }}" class="btn-book-now" style="background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%); color: #000; font-weight: 700; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; transition: all 0.2s;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-airplane" style="font-size: 48px; color: #FFD700;"></i>
                <h4 class="mt-3">No Umrah by Air Packages Available</h4>
                <p style="color: #666;">Please check back later or contact us for custom packages.</p>
            </div>
            @endif
        </section>

        <!-- 🛂 Saudi Visas Section -->
        <section id="visa-tab" class="tab-content d-none">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="visa-card" style="background: #0b0b0b; border: 1px solid #1a1a1a; border-radius: 18px; padding: 35px;">
                        <h2 style="font-weight: 800; margin-bottom: 10px; color: #fff;">Apply for Saudi Visa</h2>
                        <p style="color: #888; font-size: 14px; margin-bottom: 30px;">Get your Saudi Tourist, Umrah, or 1-Year Multiple Entry Visa processed online quickly.</p>
                        
                        <div id="visa-alert" class="alert alert-danger d-none" style="background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.4); color: #fca5a5; border-radius: 8px;"></div>
                        <div id="visa-success" class="alert alert-success d-none" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.4); color: #a7f3d0; border-radius: 8px;"></div>

                        <form id="saudi-visa-form" onsubmit="submitSaudiVisa(event)" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size: 13px; color: #aaa;">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control visa-input" required placeholder="As in Passport" style="background: #111; border: 1px solid #222; color: #fff; padding: 11px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size: 13px; color: #aaa;">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control visa-input" required placeholder="you@example.com" style="background: #111; border: 1px solid #222; color: #fff; padding: 11px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size: 13px; color: #aaa;">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control visa-input" required placeholder="+971 50 123 4567" style="background: #111; border: 1px solid #222; color: #fff; padding: 11px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size: 13px; color: #aaa;">Nationality <span class="text-danger">*</span></label>
                                    <input type="text" name="nationality" class="form-control visa-input" required placeholder="Country name" style="background: #111; border: 1px solid #222; color: #fff; padding: 11px;">
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label" style="font-size: 13px; color: #aaa;">Visa Type <span class="text-danger">*</span></label>
                                    <select name="saudi_visa_type_id" id="visa-type-select" onchange="updateVisaPrice()" class="form-select visa-input" required style="background: #111; border: 1px solid #222; color: #fff; padding: 11px;">
                                        <option value="">Select Visa Type...</option>
                                        @foreach($saudiVisas as $visa)
                                        <option value="{{ $visa->id }}" data-price="{{ $visa->price }}">{{ $visa->name }} (AED {{ number_format($visa->price, 0) }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" style="font-size: 13px; color: #aaa;">Passport Copy (PDF/JPG/PNG) <span class="text-danger">*</span></label>
                                    <input type="file" name="passport_copy" class="form-control visa-input" required accept=".pdf,.jpg,.jpeg,.png" style="background: #111; border: 1px solid #222; color: #fff; padding: 11px;">
                                    <small style="color: #666; font-size: 11px;">Maximum file size 4MB</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size: 13px; color: #aaa;">Additional Documents (Optional)</label>
                                    <input type="file" name="additional_document" class="form-control visa-input" accept=".pdf,.jpg,.jpeg,.png" style="background: #111; border: 1px solid #222; color: #fff; padding: 11px;">
                                    <small style="color: #666; font-size: 11px;">E.g. UAE Resident Visa copy (Max 4MB)</small>
                                </div>
                            </div>

                            <div class="total-price-wrap mt-4 p-3 d-flex align-items-center justify-content-between" style="background: #111; border: 1px solid #222; border-radius: 10px;">
                                <div>
                                    <span style="font-size: 12px; color: #888;">Total Amount</span>
                                    <div style="font-size: 24px; font-weight: 800; color: #FFD700;" id="display-price">AED 0</div>
                                </div>
                                <button type="submit" id="visa-submit-btn" class="btn" style="background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%); color: #000; font-weight: 700; padding: 12px 35px; border-radius: 8px;">
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

<style>
    .premium-nav-tabs {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .premium-nav-tabs::-webkit-scrollbar {
        display: none;
    }
    .tab-btn {
        background: transparent !important;
        border: none !important;
        border-bottom: 3px solid transparent !important;
        color: rgba(255, 255, 255, 0.6) !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        padding: 18px 24px !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        outline: none;
        border-radius: 0 !important;
        box-shadow: none !important;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .tab-btn i {
        font-size: 17px;
        transition: transform 0.3s ease;
    }
    .tab-btn:hover {
        color: #fff !important;
        border-bottom-color: rgba(255, 255, 255, 0.2) !important;
    }
    .tab-btn:hover i {
        transform: translateY(-2px);
    }
    .tab-btn.active {
        color: #FFD700 !important;
        border-bottom-color: #FFD700 !important;
        font-weight: 800 !important;
    }
    .tab-btn.active i {
        color: #FFD700;
        transform: scale(1.1);
    }
    .tab-section {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .tab-content {
        padding-top: 25px !important;
        padding-bottom: 60px !important;
    }
    html[data-theme="light"] .tab-btn {
        color: rgba(0, 0, 0, 0.6) !important;
    }
    html[data-theme="light"] .tab-btn:hover {
        color: #000 !important;
        border-bottom-color: rgba(0, 0, 0, 0.15) !important;
    }
    html[data-theme="light"] .tab-btn.active {
        color: #D4AF37 !important;
        border-bottom-color: #D4AF37 !important;
    }
    html[data-theme="light"] .tab-section {
        background: #f8f9fa !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
    }
    .filter-btn {
        background: #111;
        border: 1px solid #222;
        color: #ccc;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-btn.active, .filter-btn:hover {
        background: #FFD700;
        border-color: #FFD700;
        color: #000;
    }
    .package-card:hover {
        border-color: #FFD700 !important;
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(255,215,0,0.05);
    }
    .btn-book-now:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(255,215,0,0.25);
    }
    .visa-input:focus {
        border-color: #FFD700 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.15) !important;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>

<script>
function switchTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(c => {
        c.classList.add('d-none');
        c.classList.remove('active');
    });
    
    // Deactivate all tab buttons
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active');
    });
    
    // Show active tab content
    const activeSection = document.getElementById(tabId);
    activeSection.classList.remove('d-none');
    activeSection.classList.add('active');
    
    // Highlight active tab button
    event.currentTarget.classList.add('active');
}

function filterCategory(cat, type = 'bus') {
    // Highlight active filter button in current tab
    const tabSection = type === 'air' ? document.getElementById('air-tab') : document.getElementById('bus-tab');
    tabSection.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // Filter packages
    const selector = type === 'air' ? '.package-item-air' : '.package-item';
    tabSection.querySelectorAll(selector).forEach(item => {
        if (cat === 'all' || item.getAttribute('data-category') === cat) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function updateVisaPrice() {
    const select = document.getElementById('visa-type-select');
    const option = select.options[select.selectedIndex];
    const price = option.getAttribute('data-price');
    const display = document.getElementById('display-price');
    
    if (price) {
        display.textContent = 'AED ' + parseFloat(price).toLocaleString();
    } else {
        display.textContent = 'AED 0';
    }
}

function submitSaudiVisa(event) {
    event.preventDefault();
    
    const form = document.getElementById('saudi-visa-form');
    const submitBtn = document.getElementById('visa-submit-btn');
    const alertBox = document.getElementById('visa-alert');
    const successBox = document.getElementById('visa-success');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    alertBox.classList.add('d-none');
    successBox.classList.add('d-none');
    
    const formData = new FormData(form);
    
    fetch('{{ route('saudi-visa.submit') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.checkout_url) {
            successBox.textContent = 'Visa application created! Redirecting to payment checkout...';
            successBox.classList.remove('d-none');
            window.location.href = data.checkout_url;
        } else {
            alertBox.textContent = data.error || 'Visa application failed. Please check inputs.';
            alertBox.classList.remove('d-none');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Application';
        }
    })
    .catch(err => {
        console.error('Visa submit error:', err);
        alertBox.textContent = 'Something went wrong. Please check files size and try again.';
        alertBox.classList.remove('d-none');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Application';
    });
}
</script>

@include('footer')
