<style>
  .hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
  }

  .hero-content h1 {
    font-size: 3rem;
    font-weight: bold;
  }

  .search-box {
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    border-radius: 16px;
    padding: 28px 40px 24px 40px;
    max-width: 1080px;  /* Increase for horizontal length */
    margin-left: auto;
    margin-right: auto;
    font-family: 'Segoe UI', sans-serif;
  }

  .form-control, .form-select {
    border-radius: 10px;
  }

  .search-btn, .btn-search {
    background-color: rgb(255, 210, 63, 0.7);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 7px 60px;
    font-family: 'Segoe UI', sans-serif;
    font-weight: 500;
  }

  .search-btn:hover,
  .btn-search:hover {
    background-color: rgb(255, 210, 63, 0.75);
  }

  .form-label {
    font-size: 0.95rem;
  }

  @media (max-width: 1200px) {
    .search-box { max-width: 98vw; padding: 12px 4vw; }
  }

  @media (max-width: 991.98px) {
    .search-box { padding: 12px 2vw; }
    .form-label { font-size: 0.89rem; }
  }

  @media (max-width: 767.98px) {
    .search-box { padding: 10px 1vw; }
    .search-btn, .btn-search { width: 100%; padding: 10px; }
  }
</style>

<!-- Hero Section -->
<div class="">
  <div class="container hero-content">
    <!-- Search Form -->
    <div class="search-box">
      <form>
        <div class="row g-2 align-items-end">
          <div class="col-12 col-md-4">
            <label class="form-label">Pick-up location</label>
            <input type="text" class="form-control" placeholder="City, airport or station">
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Pick-up date</label>
            <input type="date" class="form-control" value="2025-05-28">
          </div>
          <div class="col-6 col-md-1">
            <label class="form-label">Time</label>
            <input type="time" class="form-control" value="10:00">
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Drop-off date</label>
            <input type="date" class="form-control" value="2025-06-04">
          </div>
          <div class="col-6 col-md-1">
            <label class="form-label">Time</label>
            <input type="time" class="form-control" value="10:00">
          </div>
          <div class="col-12 col-md-2 mt-2 mt-md-0 d-grid">
            <button type="submit" class="btn btn-search search-btn">Search</button>
          </div>
        </div>

        <div class="row mt-3 align-items-center">
          <div class="col-12 col-md-9 d-flex flex-wrap align-items-center gap-4">
            <div class="form-check me-2">
              <input class="form-check-input" type="checkbox" id="age25to70" checked>
              <label class="form-check-label" for="age25to70">Driver aged between 25 - 70</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="diffLocation">
              <label class="form-check-label" for="diffLocation">Return car to a different location</label>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
