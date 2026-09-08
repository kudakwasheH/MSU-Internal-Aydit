<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MSU Internal Audit</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width: 600px; width: 100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #004ea1 0%, #003373 100%); padding: 36px 30px 28px; border-radius: 16px 16px 0 0; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto 16px;">
                                <tr>
                                    <td style="background-color: #ffffff; padding: 8px 18px; border-radius: 10px; text-align: center;">
                                        <img src="{{ $message->embed(public_path('images/main-logo.png')) }}" alt="Midlands State University" style="height: 46px; max-width: 220px; width: auto; display: block; margin: 0 auto;">
                                    </td>
                                </tr>
                            </table>
                            <h1 style="color: #ffffff; font-size: 22px; font-weight: 700; margin: 0 0 6px; letter-spacing: -0.3px;">Welcome to MSU Internal Audit</h1>
                            <p style="color: rgba(255, 255, 255, 0.85); font-size: 13px; margin: 0;">Management System &bull; Midlands State University</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background-color: #ffffff; padding: 40px;">
                            <p style="color: #1e293b; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Dear <strong>{{ $userName }}</strong>,
                            </p>
                            <p style="color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 28px;">
                                Your account has been created on the <strong>MSU Internal Audit Management System</strong>. You can now access the system using your university Google Workspace account.
                            </p>

                            {{-- Account Details Card --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="color: #004ea1; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 16px;">Your Account Details</h3>

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</span><br>
                                                    <span style="color: #1e293b; font-size: 15px; font-weight: 600;">{{ $userName }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Email</span><br>
                                                    <span style="color: #1e293b; font-size: 15px; font-weight: 600;">{{ $userEmail }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Staff ID</span><br>
                                                    <span style="color: #1e293b; font-size: 15px; font-weight: 600;">{{ $userStaffId }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Department</span><br>
                                                    <span style="color: #1e293b; font-size: 15px; font-weight: 600;">{{ $userDepartment }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Position</span><br>
                                                    <span style="color: #1e293b; font-size: 15px; font-weight: 600;">{{ $userPosition }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span style="color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">System Role</span><br>
                                                    <span style="color: #004ea1; font-size: 15px; font-weight: 700;">{{ $userRole }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}" target="_blank"
                                           style="display: inline-block; background-color: #004ea1; color: #ffffff; font-size: 15px; font-weight: 700; text-decoration: none; padding: 16px 48px; border-radius: 12px; letter-spacing: 0.3px;">
                                            Sign In to the System &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Google SSO Notice --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="color: #1e40af; font-size: 13px; line-height: 1.6; margin: 0;">
                                            <strong>🔐 How to sign in:</strong> Click the button above and select <strong>"Sign in with Google"</strong> using your university email (<strong>{{ $userEmail }}</strong>). No password is required — authentication is handled securely via Google Workspace SSO.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #64748b; font-size: 13px; line-height: 1.6; margin: 0;">
                                If you have any questions or need assistance, please contact the Internal Audit department.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 40px; border-radius: 0 0 16px 16px; text-align: center;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0 0 4px;">
                                &copy; {{ date('Y') }} Midlands State University &mdash; Internal Audit Department
                            </p>
                            <p style="color: #cbd5e1; font-size: 11px; margin: 0;">
                                This is an automated notification. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
