<?php
/**
 * Unified Admin Panel Styles
 * Include this file in all admin pages for consistent design
 */
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+Telugu:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        --bg-gradient: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --card-hover-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        --accent-blue: #3b82f6;
        --accent-green: #10b981;
        --accent-orange: #f59e0b;
        --accent-purple: #8b5cf6;
        --accent-red: #ef4444;
        --accent-teal: #14b8a6;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--bg-gradient);
        min-height: 100vh;
        color: var(--text-dark);
        padding: 0;
    }
    
    /* Admin Wrapper */
    .admin-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }
    
    /* Admin Container */
    .admin-container { 
        max-width: 1400px; 
        margin: 2rem auto; 
        background: #fff; 
        border-radius: 16px; 
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }
    
    /* Admin Header */
    .admin-header {
        background: var(--primary-gradient);
        color: white;
        padding: 2rem 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .admin-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .admin-header-left i {
        font-size: 2.5rem;
        opacity: 0.9;
    }
    
    .admin-header-info h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    
    .admin-header-info p {
        margin: 0.25rem 0 0 0;
        opacity: 0.85;
        font-size: 0.95rem;
    }
    
    .admin-header-right {
        display: flex;
        gap: 0.75rem;
    }
    
    /* Buttons */
    .btn-back {
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1px solid rgba(255,255,255,0.25);
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-back:hover {
        background: rgba(255,255,255,0.25);
        color: white;
        text-decoration: none;
    }
    
    .btn-view-site {
        background: rgba(16, 185, 129, 0.2);
        color: #a7f3d0;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-view-site:hover {
        background: rgba(16, 185, 129, 0.4);
        color: white;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--accent-blue) 0%, #2563eb 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.35);
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }
    
    .btn-success {
        background: linear-gradient(135deg, var(--accent-green) 0%, #059669 100%);
        border: none;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.35);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, var(--accent-red) 0%, #dc2626 100%);
        border: none;
    }
    
    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        border: none;
        color: white;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #475569 0%, #334155 100%);
        color: white;
    }
    
    /* Content Section */
    .content-section {
        background: #fff;
        margin: 1.5rem;
        padding: 2rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .content-section h3 {
        color: var(--text-dark);
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border-color);
    }
    
    .content-section h3 i {
        color: var(--accent-blue);
    }
    
    /* Form Styles */
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid var(--border-color);
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    textarea.form-control {
        min-height: 120px;
    }
    
    .help-text {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
    }
    
    /* Alert Styles */
    .alert {
        border-radius: 10px;
        margin: 1.5rem;
        padding: 1rem 1.5rem;
        border: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        color: #065f46;
        border-left: 4px solid var(--accent-green);
    }
    
    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #991b1b;
        border-left: 4px solid var(--accent-red);
    }
    
    .alert-info {
        background: rgba(59, 130, 246, 0.1);
        color: #1e40af;
        border-left: 4px solid var(--accent-blue);
    }
    
    .alert-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #92400e;
        border-left: 4px solid var(--accent-orange);
    }
    
    /* Card Grid */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    .item-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }
    
    .item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }
    
    .item-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    
    .item-card-body {
        padding: 1.25rem;
    }
    
    .item-card-title {
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }
    
    /* Tabs */
    .nav-tabs {
        border-bottom: 2px solid var(--border-color);
        gap: 0.5rem;
        padding: 0 1.5rem;
        background: #f8fafc;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: var(--text-muted);
        font-weight: 500;
        padding: 1rem 1.5rem;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s;
    }
    
    .nav-tabs .nav-link:hover {
        color: var(--accent-blue);
        background: rgba(59, 130, 246, 0.05);
    }
    
    .nav-tabs .nav-link.active {
        color: var(--accent-blue);
        background: white;
        border-bottom: 3px solid var(--accent-blue);
        font-weight: 600;
    }
    
    .tab-content {
        padding: 2rem;
    }
    
    /* Tables */
    .table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table thead th {
        background: #f8fafc;
        color: var(--text-dark);
        font-weight: 600;
        border-bottom: 2px solid var(--border-color);
        padding: 1rem;
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    /* Badges */
    .badge {
        font-weight: 500;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
    }
    
    /* Telugu Font */
    .telugu-input, .telugu-text {
        font-family: 'Noto Sans Telugu', sans-serif;
    }
    
    /* Dividers */
    .section-divider {
        border-top: 2px dashed var(--border-color);
        margin: 2rem 0;
    }
    
    /* Preview Images */
    .preview-image {
        max-width: 100px;
        height: auto;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 0.25rem;
    }
    
    /* Stats Badge */
    .stats-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent-blue);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .admin-wrapper {
            padding: 1rem;
        }
        
        .admin-header {
            padding: 1.5rem;
            flex-direction: column;
            text-align: center;
        }
        
        .admin-header-left {
            flex-direction: column;
        }
        
        .admin-header-right {
            width: 100%;
            justify-content: center;
        }
        
        .content-section {
            margin: 1rem;
            padding: 1.25rem;
        }
        
        .card-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
