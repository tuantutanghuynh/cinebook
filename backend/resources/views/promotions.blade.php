{{--
/**
 * Promotions Page
 * 
 * Display all cinema promotions and special offers including:
 * - Cinema gifts and combos
 * - Member rewards and benefits
 * - Student discounts
 * - Birthday specials
 * - Seasonal promotions
 * - Tab-based navigation for different categories
 */
--}}
@extends('layouts.main')

@section('title', 'Special Promotions - TCA Cine')

@push('styles')
<style>
    /* ==================== Promotions Page Styles ==================== */
    .promotions-page {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
    }

    .promotions-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .promotions-header h1 {
        font-size: 2.5rem;
        color: var(--color-primary, #1a2233);
        margin-bottom: 10px;
    }

    .promotions-header p {
        font-size: 1.1rem;
        color: #666;
    }

    /* Tab Navigation */
    .promotion-tabs {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 40px;
        border-bottom: 2px solid #e0e0e0;
        flex-wrap: wrap;
    }

    .tab-btn {
        background: none;
        border: none;
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 500;
        color: #666;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .tab-btn:hover {
        color: var(--color-primary, #1a2233);
        background: #f8f9fa;
    }

    .tab-btn.active {
        color: var(--color-primary, #1a2233);
        border-bottom-color: var(--color-accent, #f7c873);
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Promotion Cards */
    .promotion-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .promotion-card {
        background: linear-gradient(135deg, #fffbf0 0%, #fff 100%);
        border: 2px solid #f7c873;
        border-radius: 12px;
        padding: 25px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(247, 200, 115, 0.2);
    }

    .promotion-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(247, 200, 115, 0.3);
    }

    .promotion-card h3 {
        font-size: 1.4rem;
        color: var(--color-primary, #1a2233);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .promotion-card .icon {
        font-size: 1.8rem;
    }

    .promotion-card p {
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .promotion-details {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }

    .promotion-details h4 {
        color: #008080;
        margin-bottom: 10px;
    }

    .promotion-details ul {
        list-style: none;
        padding-left: 0;
    }

    .promotion-details li {
        padding: 5px 0;
        color: #555;
    }

    .promotion-details li::before {
        content: "✓ ";
        color: #008080;
        font-weight: bold;
        margin-right: 8px;
    }

    .promotion-cta {
        display: inline-block;
        background: linear-gradient(135deg, #f7c873 0%, #e6a040 100%);
        color: #1a2233;
        padding: 12px 25px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(247, 200, 115, 0.3);
    }

    .promotion-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(247, 200, 115, 0.4);
    }

    .validity {
        color: #999;
        font-size: 0.9rem;
        margin-top: 10px;
        font-style: italic;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .promotion-grid {
            grid-template-columns: 1fr;
        }

        .promotion-tabs {
            flex-direction: column;
        }

        .tab-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="promotions-page">
    <div class="promotions-header">
        <h1>🎁 Special Promotions & Offers</h1>
        <p>Enjoy amazing deals and exclusive benefits at TCA Cine</p>
    </div>

    <!-- Tab Navigation -->
    <div class="promotion-tabs">
        <button class="tab-btn active" onclick="openTab(event, 'cinema-gifts')">🎟️ Cinema Gifts</button>
        <button class="tab-btn" onclick="openTab(event, 'member-rewards')">👑 Member Rewards</button>
        <button class="tab-btn" onclick="openTab(event, 'student-deals')">🎓 Student Deals</button>
        <button class="tab-btn" onclick="openTab(event, 'seasonal')">🎉 Seasonal Offers</button>
    </div>

    <!-- Cinema Gifts Tab -->
    <div id="cinema-gifts" class="tab-content active">
        <div class="promotion-grid">
            @if(isset($promotions['cinema-gifts']))
                @foreach($promotions['cinema-gifts'] as $promotion)
                    <div class="promotion-card">
                        <h3><span class="icon">{{ $promotion->icon }}</span> {{ $promotion->title }}</h3>
                        <p>{{ $promotion->description }}</p>
                        
                        <div class="promotion-details">
                            <h4>{{ $promotion->details_title }}</h4>
                            <ul>
                                @foreach($promotion->details_items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <a href="{{ $promotion->cta_link }}" class="promotion-cta">{{ $promotion->cta_text }}</a>
                        <p class="validity">{{ $promotion->validity_text }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Member Rewards Tab -->
    <div id="member-rewards" class="tab-content">
        <div class="promotion-grid">
            @if(isset($promotions['member-rewards']))
                @foreach($promotions['member-rewards'] as $promotion)
                    <div class="promotion-card">
                        <h3><span class="icon">{{ $promotion->icon }}</span> {{ $promotion->title }}</h3>
                        <p>{{ $promotion->description }}</p>
                        
                        <div class="promotion-details">
                            <h4>{{ $promotion->details_title }}</h4>
                            <ul>
                                @foreach($promotion->details_items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <a href="{{ $promotion->cta_link }}" class="promotion-cta">{{ $promotion->cta_text }}</a>
                        <p class="validity">{{ $promotion->validity_text }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Student Deals Tab -->
    <div id="student-deals" class="tab-content">
        <div class="promotion-grid">
            @if(isset($promotions['student-deals']))
                @foreach($promotions['student-deals'] as $promotion)
                    <div class="promotion-card">
                        <h3><span class="icon">{{ $promotion->icon }}</span> {{ $promotion->title }}</h3>
                        <p>{{ $promotion->description }}</p>
                        
                        <div class="promotion-details">
                            <h4>{{ $promotion->details_title }}</h4>
                            <ul>
                                @foreach($promotion->details_items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <a href="{{ $promotion->cta_link }}" class="promotion-cta">{{ $promotion->cta_text }}</a>
                        <p class="validity">{{ $promotion->validity_text }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Seasonal Offers Tab -->
    <div id="seasonal" class="tab-content">
        <div class="promotion-grid">
            @if(isset($promotions['seasonal']))
                @foreach($promotions['seasonal'] as $promotion)
                    <div class="promotion-card">
                        <h3><span class="icon">{{ $promotion->icon }}</span> {{ $promotion->title }}</h3>
                        <p>{{ $promotion->description }}</p>
                        
                        <div class="promotion-details">
                            <h4>{{ $promotion->details_title }}</h4>
                            <ul>
                                @foreach($promotion->details_items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <a href="{{ $promotion->cta_link }}" class="promotion-cta">{{ $promotion->cta_text }}</a>
                        <p class="validity">{{ $promotion->validity_text }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<script>
/**
 * Tab switching functionality
 * 
 * @param {Event} event - Click event
 * @param {string} tabName - ID of tab to show
 */
function openTab(event, tabName) {
    // Hide all tab contents
    const tabContents = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove('active');
    }

    // Remove active class from all buttons
    const tabBtns = document.getElementsByClassName('tab-btn');
    for (let i = 0; i < tabBtns.length; i++) {
        tabBtns[i].classList.remove('active');
    }

    // Show current tab and mark button as active
    document.getElementById(tabName).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>
@endsection
