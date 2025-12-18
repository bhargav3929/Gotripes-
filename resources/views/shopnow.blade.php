@include('header')

<style>
    .pay-page-body {
        background-color: #000;
        color: #fff;
        font-family: 'R', sans-serif;
    }

    .pay-section {
        padding-top: 150px;
        padding-bottom: 100px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #000;
        background-image: radial-gradient(circle at 50% 50%, #1a1a1a 0%, #000 70%);
    }

    .pay-card {
        background: #111;
        border: 1px solid #333;
        border-radius: 4px;
        padding: 40px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.8), 0 0 20px rgba(255, 215, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .pay-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #FFD700, #debb55, #FFD700);
    }

    .pay-title {
        color: #FFD700;
        text-align: center;
        margin-bottom: 30px;
        font-family: 'B', sans-serif;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 24px;
    }

    .form-label {
        color: #ddd;
        font-size: 13px;
        margin-bottom: 8px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .form-control,
    .form-select {
        background-color: #1a1a1a;
        border: 1px solid #333;
        color: #fff;
        border-radius: 4px;
        padding: 14px;
        transition: all 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #222;
        color: #fff;
        border-color: #FFD700;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.1);
        outline: none;
    }

    .btn-pay {
        background: linear-gradient(135deg, #FFD700 0%, #b8860b 100%);
        color: #000;
        font-weight: 800;
        font-family: 'B', sans-serif;
        width: 100%;
        padding: 16px;
        margin-top: 30px;
        border: none;
        border-radius: 4px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        cursor: pointer;
    }

    .btn-pay:hover {
        background: #fff;
        color: #000;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
    }

    .btn-pay:disabled {
        background: #444;
        color: #888;
        cursor: not-allowed;
        transform: none;
    }

    .input-group-text {
        background-color: #333;
        border: 1px solid #444;
        color: #FFD700;
    }
</style>

<div class="pay-page-body">
    <section class="pay-section">
        <div class="pay-card">
            <h2 class="pay-title">Agent Pay Online</h2>

            <form id="agentPayForm">
                @csrf

                <div class="mb-3">
                    <label for="agent_name" class="form-label">Agent Name / ID</label>
                    <input type="text" class="form-control" id="agent_name" name="agent_name"
                        placeholder="Enter Agent Name" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="client_name" class="form-label">Client Full Name</label>
                        <input type="text" class="form-control" id="client_name" name="client_name"
                            placeholder="John Doe" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="client_email" class="form-label">Client Email</label>
                        <input type="email" class="form-control" id="client_email" name="client_email"
                            placeholder="client@example.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="client_phone" class="form-label">Client Phone Number</label>
                    <input type="tel" class="form-control" id="client_phone" name="client_phone"
                        placeholder="+971 50 123 4567" required>
                </div>

                <div class="mb-3">
                    <label for="service" class="form-label">Requested Service</label>
                    <select class="form-select" id="service" name="service" required>
                        <option value="" selected disabled>-- Select Service --</option>
                        <option value="Visa">Visa Service</option>
                        <option value="World Tour Package">World Tour Package</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Processing Amount (AED)</label>
                    <div class="input-group">
                        <span class="input-group-text">AED</span>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="0.00" min="1"
                            step="0.01" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-pay" id="submitBtn">Proceed to CCAvenue</button>
            </form>
        </div>
    </section>
</div>

<!-- Hidden Form for CCAvenue Submission -->
<form id="ccavenueForm" method="post"
    action="{{ config('services.ccavenue.url') }}" style="display: none;">
    <input type="hidden" name="encRequest" id="encRequest">
    <input type="hidden" name="access_code" id="access_code">
</form>

<script>
    const ccavenueUrl = @json(config('services.ccavenue.url'));

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('agentPayForm');
        var btn = document.getElementById('submitBtn');

        if (!form) {
            console.error('Payment form not found');
            return;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'INITIALIZING SECURE GATEWAY...';

            // Get CSRF token
            var csrfToken = document.querySelector('input[name="_token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Security token missing. Please refresh the page and try again.');
                btn.disabled = false;
                btn.innerText = originalText;
                return;
            }

            // Use FormData directly instead of URLSearchParams for better compatibility
            var formData = new FormData(form);

            fetch("{{ route('agent.pay') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken.value
                },
                body: formData,
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    // Check if response is actually JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            console.error('Non-JSON response:', text);
                            throw new Error('Server returned an invalid response. Please try again.');
                        });
                    }

                    // Parse JSON response
                    return response.json().then(data => {
                        if (!response.ok) {
                            // Response was not OK, throw the error data
                            throw data;
                        }
                        return data;
                    });
                })
                .then(data => {
                    console.log('Payment response received:', data);
                    console.log('Response keys:', Object.keys(data));
                    
                    // Handle both camelCase and snake_case response formats
                    var encryptedData = data.encrypted_data || data.encryptedData;
                    var accessCode = data.access_code || data.accessCode;
                    var gatewayUrl = data.ccavenue_url || ccavenueUrl;
                    
                    if (data.success && encryptedData && accessCode && gatewayUrl) {
                        // Create CCAvenue form dynamically (same pattern as working dubai-global-village)
                        console.log('Creating CCAvenue form with encrypted data...');
                        
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = gatewayUrl;
                        form.style.display = 'none';
                        
                        const encRequest = document.createElement('input');
                        encRequest.type = 'hidden';
                        encRequest.name = 'encRequest';
                        encRequest.value = encryptedData; // Browser will automatically URL encode on form submit
                        form.appendChild(encRequest);
                        
                        const accessCodeInput = document.createElement('input');
                        accessCodeInput.type = 'hidden';
                        accessCodeInput.name = 'access_code';
                        accessCodeInput.value = accessCode;
                        form.appendChild(accessCodeInput);
                        
                        // Verify values before appending to body
                        console.log('Form values before submit:', {
                            'encRequest length': encryptedData ? encryptedData.length : 0,
                            'encRequest preview': encryptedData ? encryptedData.substring(0, 50) + '...' : 'MISSING',
                            'access_code': accessCode,
                            'form action': form.action,
                            'form method': form.method
                        });
                        
                        document.body.appendChild(form);
                        console.log('Submitting to CCAvenue...');
                        form.submit();
                    } else {
                        console.error('Missing data in response:', {
                            success: data.success,
                            has_encrypted_data: !!encryptedData,
                            has_access_code: !!accessCode,
                            has_gateway_url: !!gatewayUrl,
                            encrypted_data_length: encryptedData ? encryptedData.length : 0
                        });
                        throw new Error(data.message || 'Payment initiation failed. Missing required data.');
                    }
                })
                .catch(error => {
                    console.error('Payment Error:', error);
                    console.error('Error details:', JSON.stringify(error, null, 2));
                    
                    var message = 'An error occurred while initiating payment.';
                    
                    if (error.errors) {
                        // Validation errors
                        var errorMessages = [];
                        if (typeof error.errors === 'object') {
                            for (var field in error.errors) {
                                if (error.errors.hasOwnProperty(field)) {
                                    errorMessages.push(error.errors[field].join(', '));
                                }
                            }
                        }
                        message = errorMessages.length > 0 ? errorMessages.join('\n') : 'Please check all fields and try again.';
                    } else if (error.message) {
                        message = error.message;
                    } else if (typeof error === 'string') {
                        message = error;
                    }
                    
                    alert(message);
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
        });
    });
</script>

@include('footer')
