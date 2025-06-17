<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubuntu Marketplace Footer</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body.demo-body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            min-height: 30vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
            padding: 50px 0;
            text-align: center;
            color: #666;
        }
        
        .footer {
            background: linear-gradient(135deg, #FFD700 0%, #32CD32 100%);
            color: #333;
            position: relative;
            overflow: hidden;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FFD700, #32CD32, #FF6B35, #1E3A8A, #DC2626, #000000);
        }
        
        .footer-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 2px, transparent 2px),
                radial-gradient(circle at 80% 80%, rgba(0,0,0,0.1) 1px, transparent 1px);
            background-size: 30px 30px, 20px 20px;
        }
        
        .footer-content {
            position: relative;
            z-index: 2;
            padding: 60px 0 20px;
        }
        
        .footer-section {
            margin-bottom: 40px;
        }
        
        .footer-section h5 {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .footer-section ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-section ul li {
            margin-bottom: 10px;
        }
        
        .footer-section ul li a {
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            padding: 5px 0;
        }
        
        .footer-section ul li a:hover {
            color: #000;
            transform: translateX(5px);
        }
        
        .footer-section ul li a i {
            margin-right: 8px;
            width: 20px;
        }
        
        .brand-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .brand-logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .footer-bottom {
            background: rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            text-align: center;
            color: #2c3e50;
            font-size: 0.9rem;
        }
        
        .africa-image {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.1;
            font-size: 150px;
            color: #2c3e50;
        }
        
        .contact-card {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 2px solid #FFD700;
            margin: 0;
            height: fit-content;
        }
        
        .contact-card p {
            margin-bottom: 10px;
            color: white;
            font-size: 1rem;
        }
        
        .contact-card i {
            margin-right: 10px;
            color: #FFD700;
            width: 20px;
        }
        
        .brand-slogan {
            font-style: italic;
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .ubuntu-quote {
            color: #666;
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(44, 62, 80, 0.1);
            color: #2c3e50;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .social-icon:hover {
            background: #2c3e50;
            color: #FFD700;
            transform: translateY(-3px);
            border-color: #FFD700;
        }
        
        @media (max-width: 768px) {
            .africa-image {
                display: none;
            }
            
            .brand-logo {
                font-size: 2rem;
            }
            
            .footer-content {
                padding: 40px 0 20px;
            }
        }
        
        .flag-emoji {
            font-size: 1.5rem;
        }
    </style>
</head>
    <body class="demo-body">

    <footer class="footer">
        <div class="footer-pattern"></div>
        <i class="fas fa-map-marked-alt africa-image"></i>
        
        <div class="footer-content">
            <div class="container">
                <!-- Brand Section -->
                <div class="brand-section">
                    <div class="brand-logo">
                        <i class="fas fa-handshake"></i>
                        Ubuntu Marketplace
                        <span class="flag-emoji">🇿🇦</span>
                    </div>
                    <div class="brand-slogan">Connecting South African Communities</div>
                    <div class="ubuntu-quote">"I am because we are"</div>
                    
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="row">
                    <!-- Quick Links -->
                    <div class="col-md-3 col-sm-6">
                        <div class="footer-section">
                            <h5><i class="fas fa-link"></i> Quick Links</h5>
                            <ul>
                                <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                                <li><a href="#"><i class="fas fa-info-circle"></i> About Us</a></li>
                                <li><a href="#"><i class="fas fa-running"></i> Sports Equipment</a></li>
                                <li><a href="#"><i class="fas fa-tshirt"></i> Apparel</a></li>
                                <li><a href="#"><i class="fas fa-star"></i> Featured Products</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Customer Service -->
                    <div class="col-md-3 col-sm-6">
                        <div class="footer-section">
                            <h5><i class="fas fa-headset"></i> Customer Service</h5>
                            <ul>
                                <li><a href="#"><i class="fas fa-question-circle"></i> FAQ</a></li>
                                <li><a href="#"><i class="fas fa-undo"></i> Returns Policy</a></li>
                                <li><a href="#"><i class="fas fa-shipping-fast"></i> Shipping Info</a></li>
                                <li><a href="#"><i class="fas fa-comments"></i> Contact Support</a></li>
                                <li><a href="#"><i class="fas fa-tools"></i> Size Guide</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Legal -->
                    <div class="col-md-3 col-sm-6">
                        <div class="footer-section">
                            <h5><i class="fas fa-gavel"></i> Legal</h5>
                            <ul>
                                <li><a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a></li>
                                <li><a href="#"><i class="fas fa-file-contract"></i> Terms of Service</a></li>
                                <li><a href="#"><i class="fas fa-cookie-bite"></i> Cookie Policy</a></li>
                                <li><a href="#"><i class="fas fa-user-shield"></i> Data Protection</a></li>
                                <li><a href="#"><i class="fas fa-balance-scale"></i> Disclaimer</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Contact Card -->
                    <div class="col-md-3 col-sm-6">
                        <div class="contact-card">
                            <h5 style="color: white; margin-bottom: 20px;"><i class="fas fa-dumbbell"></i> M&M Sports</h5>
                            <p style="color: #FFD700; font-weight: 500; margin-bottom: 20px; font-style: italic;">
                                Your trusted partner for quality sports equipment and gear.
                            </p>
                            
                            <h6 style="color: #FFD700; margin-bottom: 15px;"><i class="fas fa-envelope"></i> Get In Touch</h6>
                            <p><i class="fas fa-map-marker-alt"></i> Cape Town, South Africa</p>
                            <p><i class="fas fa-phone"></i> +27 (0) 21 XXX XXXX</p>
                            <p><i class="fas fa-envelope"></i> info@mmsports.co.za</p>
                            <p><i class="fas fa-clock"></i> Mon-Fri: 8AM-6PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-start text-center">
                        <p class="mb-0">© 2025 Ubuntu Marketplace - M&M Sports. All Rights Reserved</p>
                    </div>
                    <div class="col-md-6 text-md-end text-center">
                        <p class="mb-0">
                            <i class="fas fa-code"></i> 
                            Built with ❤️ by <strong>Makanaka Mamutse</strong> for M&M Sports
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>