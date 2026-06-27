<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile · elegant</title>
  <!-- Font Awesome 6 (Free) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- AOS animation -->
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: #f8f4f0;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem 1rem;
    }

    /* ===== ROOT VARIABLES ===== */
    :root {
      --maroon: #7A0019;
      --maroon-dark: #4e0010;
      --maroon-light: #9e1e32;
      --gold: #F4C542;
      --gold-light: #ffda77;
      --cream: #FFF8EE;
      --cream-dark: #f5ebe0;
      --gray-800: #1f2937;
      --gray-600: #4b5563;
    }

    /* ===== GLOBAL CARD STYLING ===== */
    .profile-container {
      width: 100%;
      max-width: 1300px;
      margin: 0 auto;
    }

    .profile-card {
      background: white;
      border-radius: 40px;
      box-shadow: 0 30px 50px -20px rgba(0, 0, 0, 0.15), 0 10px 20px -8px rgba(122, 0, 25, 0.08);
      overflow: hidden;
      transition: all 0.3s ease;
      backdrop-filter: blur(2px);
      border: 1px solid rgba(255, 255, 255, 0.4);
    }

    /* ===== HEADER WITH GLASS ===== */
    .profile-header {
      background: linear-gradient(145deg, var(--maroon) 0%, #2e000a 100%);
      padding: 1.8rem 2.8rem;
      border-bottom: 4px solid var(--gold);
      position: relative;
      overflow: hidden;
    }

    .profile-header::after {
      content: "✦";
      position: absolute;
      right: 2.5rem;
      bottom: 0.5rem;
      font-size: 6rem;
      color: rgba(244, 197, 66, 0.08);
      font-weight: 300;
      pointer-events: none;
    }

    .profile-header h2 {
      color: white;
      font-weight: 700;
      font-size: 1.9rem;
      letter-spacing: -0.02em;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .profile-header h2 i {
      color: var(--gold);
      filter: drop-shadow(0 2px 6px rgba(244, 197, 66, 0.3));
    }

    .profile-header p {
      color: rgba(255, 255, 255, 0.8);
      margin: 0.4rem 0 0;
      font-size: 1rem;
      font-weight: 400;
      letter-spacing: 0.3px;
      backdrop-filter: blur(2px);
    }

    .edit-btn {
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 215, 100, 0.25);
      color: white;
      padding: 0.7rem 1.8rem;
      border-radius: 60px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.25s ease;
      letter-spacing: 0.3px;
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
    }

    .edit-btn i {
      color: var(--gold);
      font-size: 1rem;
    }

    .edit-btn:hover {
      background: var(--gold);
      color: var(--maroon-dark);
      transform: translateY(-2px) scale(1.02);
      border-color: var(--gold);
      box-shadow: 0 12px 28px rgba(244, 197, 66, 0.25);
    }

    .edit-btn:hover i {
      color: var(--maroon-dark);
    }

    /* ===== BODY ===== */
    .profile-body {
      padding: 2.8rem 2.8rem 3rem;
      background: #fcf9f6;
    }

    /* ===== INFO CARDS ===== */
    .info-card {
      background: white;
      border-radius: 28px;
      box-shadow: 0 8px 24px -6px rgba(0, 0, 0, 0.04), 0 2px 8px rgba(122, 0, 25, 0.03);
      transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
      height: 100%;
      border: 1px solid rgba(255, 215, 175, 0.2);
      overflow: hidden;
      backdrop-filter: blur(2px);
    }

    .info-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 24px 40px -16px rgba(122, 0, 25, 0.15), 0 8px 20px -8px rgba(0, 0, 0, 0.04);
      border-color: rgba(244, 197, 66, 0.3);
    }

    .info-card-header {
      padding: 1.2rem 1.8rem;
      font-weight: 700;
      font-size: 1.1rem;
      border-bottom: 2px solid;
      display: flex;
      align-items: center;
      gap: 14px;
      background: rgba(255, 248, 238, 0.3);
    }

    .info-card-header i {
      font-size: 1.4rem;
    }

    .info-card-body {
      padding: 1.8rem 1.8rem 2rem;
    }

    .info-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.85rem 0;
      border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    }

    .info-item:last-of-type {
      border-bottom: none;
    }

    .info-label {
      font-weight: 600;
      color: var(--gray-800);
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.95rem;
    }

    .info-label i {
      width: 24px;
      color: var(--maroon);
      font-size: 1rem;
      text-align: center;
    }

    .info-value {
      color: var(--gray-600);
      font-weight: 500;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .badge-gold {
      background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
      color: white;
      padding: 0.3rem 1rem;
      border-radius: 40px;
      font-weight: 600;
      font-size: 0.8rem;
      letter-spacing: 0.2px;
      box-shadow: 0 4px 8px rgba(122, 0, 25, 0.15);
    }

    .badge-soft {
      background: #f0f3f8;
      color: #1f2a3a;
      padding: 0.3rem 1rem;
      border-radius: 40px;
      font-weight: 500;
      font-size: 0.8rem;
    }

    .badge-yes {
      background: linear-gradient(135deg, #d1fae5, #a7f3d0);
      color: #065f46;
      padding: 0.3rem 1.2rem;
      border-radius: 40px;
      font-weight: 600;
      font-size: 0.75rem;
    }

    .badge-no {
      background: linear-gradient(135deg, #fee2e2, #fecaca);
      color: #991b1b;
      padding: 0.3rem 1.2rem;
      border-radius: 40px;
      font-weight: 600;
      font-size: 0.75rem;
    }

    .badge-income {
      background: linear-gradient(145deg, #dbeafe, #bfdbfe);
      color: #1e3a6b;
      padding: 0.3rem 1.2rem;
      border-radius: 40px;
      font-weight: 600;
    }

    .badge-subject {
      padding: 0.25rem 0.9rem;
      border-radius: 30px;
      font-weight: 600;
      font-size: 0.75rem;
      background: #e9edf2;
      color: #1f2a3a;
    }

    .badge-subject.bg-success-soft {
      background: #d1fae5;
      color: #065f46;
    }
    .badge-subject.bg-primary-soft {
      background: #dbeafe;
      color: #1e3a6b;
    }
    .badge-subject.bg-secondary-soft {
      background: #e5e7eb;
      color: #374151;
    }

    /* table inside card */
    .table-subjects {
      margin-top: 0.5rem;
      border-collapse: separate;
      border-spacing: 0 6px;
    }

    .table-subjects th {
      font-weight: 600;
      color: var(--gray-800);
      border-bottom: 1px solid #e9e2da;
      padding: 0.6rem 0.2rem;
      background: transparent;
      font-size: 0.8rem;
      letter-spacing: 0.3px;
    }

    .table-subjects td {
      padding: 0.5rem 0.2rem;
      background: transparent;
      border-bottom: 1px dashed #eee8e0;
      font-weight: 500;
    }

    .table-subjects tr:last-child td {
      border-bottom: none;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      text-align: center;
      padding: 3.5rem 2rem;
      background: rgba(255, 248, 238, 0.4);
      border-radius: 40px;
      backdrop-filter: blur(2px);
    }

    .empty-state i {
      font-size: 4.5rem;
      color: #d1c5b8;
      margin-bottom: 1.2rem;
      display: block;
      opacity: 0.7;
    }

    .empty-state h5 {
      font-size: 1.6rem;
      font-weight: 700;
      color: var(--gray-800);
      margin-bottom: 0.5rem;
    }

    .empty-state p {
      color: var(--gray-600);
      max-width: 380px;
      margin: 0.5rem auto 1.8rem;
      font-size: 1rem;
    }

    .create-btn {
      background: linear-gradient(145deg, #10b981, #059669);
      color: white;
      padding: 0.9rem 2.8rem;
      border-radius: 60px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.25s ease;
      box-shadow: 0 10px 24px -8px rgba(16, 185, 129, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .create-btn:hover {
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 18px 36px -10px rgba(16, 185, 129, 0.4);
      color: white;
      background: linear-gradient(145deg, #34d399, #059669);
    }

    .create-btn i {
      font-size: 1.2rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
      .profile-header {
        padding: 1.5rem 1.8rem;
      }
      .profile-header h2 {
        font-size: 1.5rem;
      }
      .profile-body {
        padding: 2rem 1.5rem;
      }
      .info-card-header {
        padding: 1rem 1.4rem;
      }
      .info-card-body {
        padding: 1.4rem;
      }
    }

    @media (max-width: 768px) {
      .profile-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
      .profile-header .d-flex {
        width: 100%;
        flex-wrap: wrap;
      }
      .edit-btn {
        width: 100%;
        justify-content: center;
        padding: 0.7rem;
      }
      .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.4rem;
        padding: 0.7rem 0;
      }
      .info-value {
        width: 100%;
        justify-content: flex-start;
      }
      .profile-body {
        padding: 1.5rem 1rem;
      }
      .empty-state i {
        font-size: 3.2rem;
      }
      .empty-state h5 {
        font-size: 1.3rem;
      }
    }

    @media (max-width: 480px) {
      .profile-header h2 {
        font-size: 1.3rem;
        flex-wrap: wrap;
      }
      .profile-header p {
        font-size: 0.85rem;
      }
      .info-card-header {
        font-size: 0.95rem;
        padding: 0.8rem 1rem;
      }
      .info-label {
        font-size: 0.85rem;
        gap: 8px;
      }
    }
  </style>
</head>
<body>

<div class="profile-container">
  <div class="profile-card" data-aos="fade-up" data-aos-duration="700">

    <!-- ========== HEADER ========== -->
    <div class="profile-header">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position: relative; z-index: 2;">
        <div>
          <h2>
            <i class="fas fa-user-astronaut"></i>
            Your Profile
          </h2>
          <p><i class="fas fa-sparkles" style="color: var(--gold); margin-right: 6px;"></i> Manage your academic & personal insights</p>
        </div>
        <!-- if profile exists, show edit button (demo: always show) -->
        <a href="#" class="edit-btn">
          <i class="fas fa-pen-fancy"></i>
          Edit Profile
        </a>
      </div>
    </div>

    <!-- ========== BODY ========== -->
    <div class="profile-body">

      <!-- ===== PROFILE EXISTS (demo) ===== -->
      <div class="row g-4">

        <!-- Academic Information -->
        <div class="col-md-6">
          <div class="info-card" data-aos="fade-up" data-aos-delay="80">
            <div class="info-card-header" style="border-bottom-color: var(--maroon);">
              <i class="fas fa-graduation-cap" style="color: var(--maroon);"></i>
              <span style="color: var(--maroon);">Academic</span>
            </div>
            <div class="info-card-body">
              <div class="info-item">
                <span class="info-label"><i class="fas fa-star"></i> Total A’s (SPM)</span>
                <span class="info-value"><span class="badge-gold">9</span></span>
              </div>
              <div class="info-item">
                <span class="info-label"><i class="fas fa-layer-group"></i> Study Level</span>
                <span class="info-value">Foundation / Matriculation</span>
              </div>
              <div class="info-item">
                <span class="info-label"><i class="fas fa-flask"></i> Field of Study</span>
                <span class="info-value">Engineering · Physics</span>
              </div>

              <!-- SPM results (beautiful table) -->
              <hr style="margin: 1rem 0 0.5rem; opacity: 0.3;">
              <div class="mt-2">
                <span class="info-label" style="margin-bottom: 0.6rem;"><i class="fas fa-table"></i> SPM Results</span>
                <table class="table-subjects w-100">
                  <thead>
                    <tr><th>Subject</th><th style="text-align: right;">Grade</th></tr>
                  </thead>
                  <tbody>
                    <tr><td>Bahasa Melayu</td><td style="text-align: right;"><span class="badge-subject bg-success-soft">A+</span></td></tr>
                    <tr><td>English</td><td style="text-align: right;"><span class="badge-subject bg-success-soft">A</span></td></tr>
                    <tr><td>Mathematics</td><td style="text-align: right;"><span class="badge-subject bg-success-soft">A+</span></td></tr>
                    <tr><td>Physics</td><td style="text-align: right;"><span class="badge-subject bg-primary-soft">B+</span></td></tr>
                    <tr><td>Chemistry</td><td style="text-align: right;"><span class="badge-subject bg-primary-soft">B</span></td></tr>
                    <tr><td>Biology</td><td style="text-align: right;"><span class="badge-subject bg-secondary-soft">C+</span></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Financial Information -->
        <div class="col-md-6">
          <div class="info-card" data-aos="fade-up" data-aos-delay="120">
            <div class="info-card-header" style="border-bottom-color: #10b981;">
              <i class="fas fa-coins" style="color: #10b981;"></i>
              <span style="color: #10b981;">Financial</span>
            </div>
            <div class="info-card-body">
              <div class="info-item">
                <span class="info-label"><i class="fas fa-chart-pie"></i> Income Category</span>
                <span class="info-value"><span class="badge-income">B40 (low income)</span></span>
              </div>
              <div class="info-item">
                <span class="info-label"><i class="fas fa-wallet"></i> Monthly Income</span>
                <span class="info-value"><span style="font-weight: 600; color: #1f2937;">RM 2,450.00</span></span>
              </div>
              <div class="info-item" style="border-bottom: none; padding-top: 0.2rem;">
                <span class="info-label"><i class="fas fa-credit-card"></i> Financial aid</span>
                <span class="info-value"><span style="background: #e6f7e6; padding: 0.2rem 0.9rem; border-radius: 30px; font-size:0.8rem;">Eligible</span></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Personal Information -->
        <div class="col-md-6">
          <div class="info-card" data-aos="fade-up" data-aos-delay="160">
            <div class="info-card-header" style="border-bottom-color: #f59e0b;">
              <i class="fas fa-id-card" style="color: #f59e0b;"></i>
              <span style="color: #b45309;">Personal</span>
            </div>
            <div class="info-card-body">
              <div class="info-item">
                <span class="info-label"><i class="fas fa-cake-candles"></i> Age</span>
                <span class="info-value">20 years</span>
              </div>
              <div class="info-item">
                <span class="info-label"><i class="fas fa-location-dot"></i> State</span>
                <span class="info-value">Selangor</span>
              </div>
              <div class="info-item" style="border-bottom: none;">
                <span class="info-label"><i class="fas fa-passport"></i> Citizenship</span>
                <span class="info-value">Malaysian</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="col-md-6">
          <div class="info-card" data-aos="fade-up" data-aos-delay="200">
            <div class="info-card-header" style="border-bottom-color: var(--gold);">
              <i class="fas fa-circle-plus" style="color: var(--gold);"></i>
              <span style="color: #92400e;">Additional</span>
            </div>
            <div class="info-card-body">
              <div class="info-item">
                <span class="info-label"><i class="fas fa-flag"></i> Bumiputera</span>
                <span class="info-value"><span class="badge-yes"><i class="fas fa-check-circle" style="margin-right: 4px;"></i> Yes</span></span>
              </div>
              <div class="info-item" style="border-bottom: none;">
                <span class="info-label"><i class="fas fa-crown"></i> Leadership</span>
                <span class="info-value"><span class="badge-no"><i class="fas fa-times-circle" style="margin-right: 4px;"></i> No</span></span>
              </div>
            </div>
          </div>
        </div>

        <!-- extra sparkle: a small note card (optional) -->
        <div class="col-12 mt-2">
          <div style="background: linear-gradient(115deg, #fcf3e8, #f8ede2); border-radius: 30px; padding: 1.2rem 2rem; border-left: 6px solid var(--gold); box-shadow: inset 0 1px 4px rgba(255,255,255,0.8);">
            <div class="d-flex align-items-center gap-3 flex-wrap">
              <i class="fas fa-lightbulb" style="font-size: 1.8rem; color: var(--gold);"></i>
              <span style="font-weight: 500; color: #3d2b1a;">Your profile is <strong style="color: var(--maroon);">92%</strong> complete — add more details to get better scholarship matches.</span>
              <a href="#" style="margin-left: auto; background: transparent; border: 1px solid var(--maroon); color: var(--maroon); padding: 0.3rem 1.6rem; border-radius: 40px; text-decoration: none; font-weight: 600; transition: 0.2s;">Update</a>
            </div>
          </div>
        </div>

      </div> <!-- /.row -->

    </div> <!-- /.profile-body -->

  </div> <!-- /.profile-card -->
</div>

<!-- ===== AOS ===== -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 700,
    once: true,
    easing: 'ease-out-cubic',
    offset: 20
  });
</script>
</body>
</html>