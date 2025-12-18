<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $isForApplicant ? 'Job Application Confirmation' : 'Job Application Received' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ffffff;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 1000px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e1e, #0f0f0f);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(255, 215, 0, 0.1), 0 0 0 1px rgba(255, 215, 0, 0.2);
        }

        .header {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid #FFD700;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 6px;
            background: linear-gradient(90deg, transparent, #FFD700, transparent);
        }

        .header h1 {
            color: #FFD700;
            font-size: 28px;
            margin: 0;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            letter-spacing: 1px;
        }

        .thank-you-section {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #000000;
            padding: 25px;
            text-align: center;
            margin-bottom: 0;
        }

        .thank-you-section h2 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: bold;
        }

        .thank-you-section p {
            margin: 0;
            font-size: 16px;
            font-weight: 500;
        }

        .flex-container {
            display: flex;
            min-height: 500px;
        }

        .left-section {
            flex: 1;
            padding: 35px 30px;
            background: linear-gradient(145deg, #1a1a1a, #0a0a0a);
        }

        .right-section {
            width: 420px;
            padding: 35px 25px;
            background: linear-gradient(145deg, #2a2a2a, #1a1a1a);
            border-left: 2px solid #FFD700;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #FFD700;
            font-size: 24px;
            margin: 0 0 25px 0;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .details-table th,
        .details-table td {
            padding: 15px 18px;
            text-align: left;
            font-size: 15px;
            border-bottom: 1px solid #333333;
        }

        .details-table th {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #000000;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
        }

        .details-table td {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }

        .details-table tr:nth-child(even) td {
            background: rgba(255, 255, 255, 0.08);
        }

        .details-table tr:hover td {
            background: rgba(255, 215, 0, 0.1);
            transition: background 0.3s ease;
        }

        .highlight {
            font-weight: bold;
            color: #FFD700;
            font-size: 16px;
            margin: 10px 0;
            display: flex;
            align-items: center;
        }

        .highlight::before {
            content: '✨';
            margin-right: 8px;
        }

        .profile-header {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #000000;
            text-align: center;
            padding: 18px;
            font-size: 18px;
            font-weight: 700;
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .photo-section {
            width: 100%;
            background: linear-gradient(145deg, #333333, #1a1a1a);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 250px;
            margin-bottom: 25px;
            border-radius: 10px;
            border: 2px solid #FFD700;
            position: relative;
            overflow: hidden;
        }

        .photo-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 49%, rgba(255, 215, 0, 0.1) 50%, transparent 51%);
        }

        .photo-section img {
            width: 180px;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #FFD700;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 1;
        }

        .no-photo {
            color: #FFD700;
            font-size: 18px;
            font-weight: 500;
        }

        .button-row {
            display: flex;
            gap: 15px;
            width: 100%;
            margin-top: 20px;
            flex-direction: column;
        }

        .action-btn {
            padding: 18px 24px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            color: #000000;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .action-btn.gold {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .action-btn.dark {
            background: linear-gradient(135deg, #4a4a4a 0%, #2a2a2a 100%);
            color: #FFD700;
            border: 2px solid #FFD700;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
        }

        .action-btn.gold:hover {
            background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
        }

        .action-btn.dark:hover {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #000000;
        }

        .footer {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            padding: 25px;
            text-align: center;
            border-top: 2px solid #FFD700;
            color: #FFD700;
        }

        .footer p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }

        @media (max-width: 900px) {
            .flex-container {
                flex-direction: column;
            }
            .right-section {
                width: 100%;
                border-left: none;
                border-top: 2px solid #FFD700;
            }
            .button-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $isForApplicant ? 'Application Confirmation' : 'New Job Application Received' }}</h1>
        </div>

        @if($isForApplicant)
            <!-- Thank you section for applicants only -->
            <div class="thank-you-section">
                <h2>Thank You for Your Application!</h2>
                <p>We have received your application and will get back to you soon. Our HR team will review your profile and contact you within 2-3 business days.</p>
            </div>
        @endif

        <div class="flex-container">
            <!-- Left: Application details -->
            <div class="left-section">
                <h2>{{ $isForApplicant ? 'Your Application Details' : 'Candidate Information' }}</h2>
                <table class="details-table">
                    <tbody>
                    @foreach($data as $key => $value)
                        @if($key !== 'resume_path' && $key !== 'passport_path' && !empty($value))
                            <tr>
                                <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                <td>
                                    @if($key == 'email')
                                        <a href="mailto:{{ $value }}" style="color: #FFD700; text-decoration: none;">{{ $value }}</a>
                                    @elseif($key == 'mobile')
                                        <a href="tel:{{ $value }}" style="color: #FFD700; text-decoration: none;">{{ $value }}</a>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                
                @if(!empty($data['resume_path']))
                    <div class="highlight">Resume attached and received</div>
                @endif
                @if(!empty($data['passport_path']))
                    <div class="highlight">Passport/ID document attached</div>
                @endif
            </div>

            @if(!$isForApplicant)
            <!-- Right: Candidate profile & action buttons (Admin view only) -->
            <div class="right-section">
                <div class="profile-header">Candidate Profile</div>
                <div class="photo-section">
                    @if(!empty($data['passport_path']))
                        <img src="{{ asset('storage/' . $data['passport_path']) }}" alt="Candidate Photo">
                    @else
                        <span class="no-photo">Photo Not Available</span>
                    @endif
                </div>
                
                <div class="button-row">
                    <!-- Gold button: Compose mail to applicant -->
                    <a href="mailto:{{ $data['email'] }}?subject=Regarding%20Your%20Job%20Application%20-%20{{ urlencode($data['name']) }}&body=Dear%20{{ urlencode($data['name']) }},%0D%0A%0D%0AThank%20you%20for%20your%20interest%20in%20working%20with%20us.%0D%0A%0D%0A"
                       class="action-btn gold">
                        Contact Candidate
                    </a>
                    
                    <!-- Dark button: Compose mail to recruiters -->
                    <a href="mailto:amer@aynalamirtourism.com?subject=Candidate%20Review%20-%20{{ urlencode($data['name']) }}&body=Please%20review%20this%20candidate%20application.%0D%0A%0D%0ACandidate:%20{{ urlencode($data['name']) }}%0D%0AProfession:%20{{ urlencode($data['profession']) }}%0D%0AExperience:%20{{ urlencode($data['experience']) }}%20years"
                       class="action-btn dark">
                        Forward to Recruiters
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
