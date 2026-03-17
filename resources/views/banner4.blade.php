<style>
  .search-box {
    background-color: #222;
    padding: 25px;
    border-radius: 16px;
    margin-top: 0px;
    color: white;
    font-family: 'Segoe UI', sans-serif;
    height: auto;
  }

  .search-box .form-control,
  .search-box .form-select {
    border-radius: 0.5rem;
    border: none;
    font-size: 15px;
  }

  .search-box .form-check-label {
    color: #ccc;
    font-size: 14px;
  }

  .btn-search {
    background-color: rgb(255, 210, 63, .7);
    border: none;
    color: white;
    padding: 10px 24px;
    font-size: 16px;
    border-radius: 0.7rem;
    font-weight: 600;
    width: 100%;
  }
  .btn-search:hover {
    background-color: rgb(255, 210, 63, .7);
  }
  .radio-group .form-check-label {
    color: white;
    font-weight: 500;
    font-size: 16px;
  }

  .radio-group {
    margin-bottom: 20px;
    flex-wrap: wrap;
  }

  .radio-group .form-check {
    margin-right: 1rem;
    margin-bottom: 0.5rem;
  }

  label {
    color: #ccc;
    font-size: 13px;
    margin-bottom: 2px;
    display: block;
  }

  .form-check-input {
    border-radius: 4px;
  }

  @media (max-width: 768px) {
    .btn-search {
      margin-top: 10px;
    }
   
  }
</style>

<div class="row " >
  <div class="col-12">
    <div class="search-box">
      <div class="d-flex flex-wrap mb-0 radio-group">
        <div class="form-check me-3">
          <input class="form-check-input" type="radio" name="tripType" id="return" checked>
          <label class="form-check-label" for="return">Return</label>
        </div>
        <div class="form-check me-3">
          <input class="form-check-input" type="radio" name="tripType" id="oneWay">
          <label class="form-check-label" for="oneWay">One way</label>
        </div>
        <div class="form-check me-3">
          <input class="form-check-input" type="radio" name="tripType" id="multiCity">
          <label class="form-check-label" for="multiCity">Multi-city</label>
        </div>
      </div>

      <div class="row g-2 mb-1">
        <div class="col-12 col-md-2">
          <label>From</label>
          <select class="form-select">
            <option>India (IN)</option>
            <option>UK</option>
            <option>Germany</option>
          </select>
        </div>

        <div class="col-12 col-md-2">
          <label>To</label>
          <select class="form-select">
            <option>United States (US)</option>
            <option>Canada</option>
            <option>Australia</option>
          </select>
        </div>

        <div class="col-6 col-md-2">
          <label>Depart</label>
          <input type="date" class="form-control" value="2025-05-31">
        </div>

        <div class="col-6 col-md-2">
          <label>Return</label>
          <input type="date" class="form-control" value="2025-06-07">
        </div>

        <div class="col-12 col-md-2">
          <label>Travellers & Class</label>
          <select class="form-select">
            <option>1 adult, Economy</option>
            <option>2 adults, Business</option>
            <option>3 adults, First</option>
          </select>
        </div>

        <div class="col-12 col-md-2 mt-3 mt-md-0 " style="padding-top:5px;">
          <button class="btn btn-search">Search</button>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-md-2 form-check " >
          <input class="form-check-input" type="checkbox" id="nearbyFrom">
          <label class="form-check-label" for="nearbyFrom">Add nearby airports</label>
        </div>

        <div class="col-12 col-md-2 form-check ">
          <input class="form-check-input" type="checkbox" id="nearbyTo">
          <label class="form-check-label" for="nearbyTo">Add nearby airports</label>
        </div>

        <div class="col-12 col-md-3 form-check ">
          <input class="form-check-input" type="checkbox" id="directFlights">
          <label class="form-check-label" for="directFlights">Direct flights only</label>
        </div>
      </div>
    </div>
  </div>
</div>
