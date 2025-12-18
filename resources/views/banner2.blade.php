<style>
  .search-box {
    background-color: #222;
    padding: 25px 40px;
    border-radius: 16px;
    margin-top: 0px;
    color: white;
    font-family: 'Segoe UI', sans-serif;
    max-width: 990px;   /* Increased length for a bigger form box */
    margin-left: auto;
    margin-right: auto;
  }

  .form-control,
  .form-select {
    border-radius: 0.4rem;
  }

  .search-btn, .btn-search {
    background-color: rgb(255, 210, 63, .7);
    color: white;
    border-radius: 0.6rem;
    border: none;
  }

  .search-btn:hover, .btn-search:hover {
    background-color: rgb(255, 210, 63, .7);
  }

  label.form-label {
    font-size: 0.9rem;
  }

  @media (max-width: 990px) {
    .search-box {
      padding: 15px 5px;
      max-width: 99vw;
    }
  }

  @media (max-width: 767.98px) {
    .search-box {
      padding: 15px 2vw;
    }
    .search-btn, .btn-search {
      margin-top: 10px;
    }
    .form-label {
      margin-left: 0 !important;
    }
  }
</style>

<div class="container hero-content">
  <!-- Search Box -->
  <div class="search-box">
    <form>
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label">Where do you want to stay?</label>
          <input type="text" class="form-control" placeholder="Enter destination or hotel name" />
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label">Check-in</label>
          <input type="date" class="form-control" value="2025-05-31" />
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label">Check-out</label>
          <input type="date" class="form-control" value="2025-06-01" />
        </div>
        <div class="col-12 col-md-2">
          <label class="form-label">Guests and rooms</label>
          <select class="form-select">
            <option>1 adult, 1 room</option>
            <option>2 adults, 1 room</option>
            <option>2 adults, 2 rooms</option>
          </select>
        </div>
        <div class="col-12 col-md-2 d-grid mt-md-4 mt-3">
          <button class="btn btn-search">Search</button>
        </div>
      </div>
      <!-- Filters -->
      <div class="row mt-3">
        <div class="col-12">
          <div class="d-flex flex-wrap align-items-center gap-3">
            <label class="fw-bold mb-0 me-3">Popular filters:</label>
            <div class="form-check d-flex align-items-center mb-0">
              <input class="form-check-input me-2" type="checkbox" id="filter1">
              <label class="form-check-label mb-0" for="filter1" style="white-space: nowrap;">Free cancellation</label>
            </div>
            <div class="form-check d-flex align-items-center mb-0">
              <input class="form-check-input me-2" type="checkbox" id="filter2">
              <label class="form-check-label mb-0" for="filter2" style="white-space: nowrap;">4 stars</label>
            </div>
            <div class="form-check d-flex align-items-center mb-0">
              <input class="form-check-input me-2" type="checkbox" id="filter3">
              <label class="form-check-label mb-0" for="filter3" style="white-space: nowrap;">3 stars</label>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
