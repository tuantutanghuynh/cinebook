{{--
/**
 * Footer Partial
 * 
 * Site-wide footer including:
 * - Brand information and contact details
 * - Quick navigation links
 * - Social media links
 * - Copyright information
 * - Additional site resources
 */
--}}
<footer class="footer">
    <div>
        <!-- Brand Section -->
        <div>
            <div class="footer-brand">
                <img src="{{ asset('images/tca-cine-logo.jpg') }}" alt="TCA Cine Logo">
                <h3>TCA Cine</h3>
            </div>
            <p>Your ultimate cinema destination.</p>
            
            <div class="footer-socials">
                <a href="{{ route('coming_soon') }}">Facebook</a> |
                <a href="{{ route('coming_soon') }}">Twitter</a> |
                <a href="{{ route('coming_soon') }}">Instagram</a>
            </div>
        </div>

        <!-- Quick Links -->
        <div>
            <h4>Quick Links</h4>
            <ul>
                <li><a href="{{ route('homepage') }}">Home</a></li>
                <li><a href="{{ route('now_showing') }}">Now Showing</a></li>
                <li><a href="{{ route('upcoming_movies') }}">Upcoming</a></li>
                <li><a href="{{ route('promotions') }}">Promotions</a></li>
                <li><a href="{{ route('sitemap') }}">📍 View Sitemap</a></li>
            </ul>
        </div>

        <!-- Contact Info -->
        <div>
            <h4>Contact Info</h4>
            <p>📍 123 Cinema Street, Ho Chi Minh City</p>
            <p>📞 +84 123 456 789</p>
            <p>📧 info@tcacine.com</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} TCA Cine. All rights reserved.</p>
        <p>
            <a href="{{ route('coming_soon') }}">Privacy Policy</a> | 
            <a href="{{ route('coming_soon') }}">Terms of Service</a> | 
            <a href="{{ route('sitemap') }}">Sitemap</a>
        </p>
    </div>
</footer>