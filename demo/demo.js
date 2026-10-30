const filter = document.querySelector('#type')
const listings = [...document.querySelectorAll('#listings article')]
const count = document.querySelector('#count')

filter.addEventListener('change', () => {
  for (const listing of listings) listing.hidden = filter.value !== 'all' && listing.dataset.type !== filter.value
  const visible = listings.filter(listing => !listing.hidden).length
  count.textContent = `${visible} fictional ${visible === 1 ? 'listing' : 'listings'}`
})
