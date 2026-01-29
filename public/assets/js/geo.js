const headers = { "X-CSCAPI-KEY": "" }; // Leave blank - use Laravel backend proxy
let countryList = [];

function loadCountries(index) {
  axios.get('/api/countries')
    .then(res => {
        console.log(res.data);
      countryList = res.data;
      const select = document.querySelector(`select[name="attendees[${index}][country]"]`);
      select.innerHTML = `<option value="">Select Country</option>`;
      countryList.forEach(c => {
        
        select.innerHTML += `<option value="${c.name}">${c.name}</option>`;
      });
    });
}

function loadStates(selectedCountryName, index) {
  const stateSelect = document.querySelector(`select[name="attendees[${index}][state]"]`);
  const citySelect = document.querySelector(`select[name="attendees[${index}][city]"]`);
  const selectedCountry = countryList.find(c => c.name === selectedCountryName);

  if (!selectedCountry) {
    stateSelect.innerHTML = `<option value="${selectedCountryName}" selected>${selectedCountryName}</option>`;
    citySelect.innerHTML = `<option value="${selectedCountryName}" selected>${selectedCountryName}</option>`;
    return;
  }

  axios.get(`/api/states/${selectedCountry.iso2}`)
    .then(res => {
      const states = res.data;
      if (states.length === 0) {
        stateSelect.innerHTML = `<option value="${selectedCountryName}" selected>${selectedCountryName}</option>`;
        citySelect.innerHTML = `<option value="${selectedCountryName}" selected>${selectedCountryName}</option>`;
      } else {
        stateSelect.innerHTML = `<option value="">Select State</option>`;
          states
            .slice() // create a shallow copy to avoid mutating original array
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach(s => {
              stateSelect.innerHTML += `<option value="${s.name}">${s.name}</option>`;
            });
      }
    });
}

function loadCities(countryName, stateName, index) {
  const citySelect = document.querySelector(`select[name="attendees[${index}][city]"]`);
  const selectedCountry = countryList.find(c => c.name === countryName);
  if (!selectedCountry) {
    citySelect.innerHTML = `<option value="${stateName}" selected>${stateName}</option>`;
    return;
  }

  axios.get(`/api/states/${selectedCountry.iso2}`)
    .then(res => {
      const matchingState = res.data.find(s => s.name === stateName);
      if (!matchingState) {
        citySelect.innerHTML = `<option value="${stateName}" selected>${stateName}</option>`;
        return;
      }
      axios.get(`/api/cities/${selectedCountry.iso2}/${matchingState.iso2}`)
        .then(res => {
          const cities = res.data;
          if (cities.length === 0) {
            citySelect.innerHTML = `<option value="${stateName}" selected>${stateName}</option>`;
          } else {
            citySelect.innerHTML = `<option value="">Select City</option>`;
            cities.forEach(c => {
              citySelect.innerHTML += `<option value="${c.name}">${c.name}</option>`;
            });
          }
        });
    });
}

function attachDropdownListeners(index) {
const countrySelect = document.querySelector(`select[name="attendees[${index}][country]"]`);
  
    if (!countrySelect) {
        console.error(`Country select not found for index ${index}`);
        return;
    }
  const stateSelect = document.querySelector(`select[name="attendees[${index}][state]"]`);

  countrySelect.addEventListener("change", function () {
    loadStates(this.value, index);
  });

  stateSelect.addEventListener("change", function () {
    loadCities(countrySelect.value, this.value, index);
  });
}
