import { faHotel, faPlane, faGlobeAmericas, faCar } from '@fortawesome/free-solid-svg-icons'
import type { FooterLink, FooterTopLink } from '../../js/Types/layout'

const footerLinks: FooterLink[] = [
  {
    title: 'Page',
    items: [
      { name: 'About us', link: { name: '' } },
      { name: 'Contact us', link: { name: '' } },
      {
        name: 'News and Blog',
        link: { name: '' }
      },
      { name: 'Meet a Team', link: { name: '' } }
    ]
  },
  {
    title: 'Link',
    items: [
      { name: 'Sign up', link: { name: '' } },
      { name: 'Sign in', link: { name: '' } },
      { name: 'Privacy Policy', link: { name: '' } },
      { name: 'Terms', link: { name: '' } },
      { name: 'Cookie' },
      { name: 'Support', link: { name: '' } }
    ]
  },
  {
    title: 'Global Site',
    items: [
      { name: 'India' },
      { name: 'California' },
      { name: 'Indonesia' },
      { name: 'Canada' },
      { name: 'Malaysia' }
    ]
  },
  {
    title: 'Booking',
    items: [
      { name: 'Hotel', icon: faHotel, link: { name: '' } },
      { name: 'Flight', icon: faPlane, link: { name: '' } },
      { name: 'Tour', icon: faGlobeAmericas, link: { name: '' } },
      { name: 'Cabs', icon: faCar, link: { name: '' } }
    ]
  }
]

const topLinks: FooterTopLink[] = [
  { name: 'Flights', link: { name: '' } },
  { name: 'Hotels', link: { name: '' } },
  { name: 'Tour', link: { name: '' } },
  { name: 'Cabs', link: { name: '' } },
  { name: 'About', link: { name: '' } },
  { name: 'Contact us', link: { name: '' } },
  { name: 'Blogs', link: { name: '' } },
  { name: 'Services', link: { name: '' } },
  { name: 'Detail page', link: { name: '' } },
  { name: 'Policy', link: { name: '' } },
  { name: 'Testimonials', link: { name: '' } },
  { name: 'Newsletters', link: { name: '' } },
  { name: 'Podcasts' },
  { name: 'Protests' },
  { name: 'NewsCyber' },
  { name: 'Education' },
  { name: 'Sports' },
  { name: 'Tech and Auto' },
  { name: 'Opinion' },
  { name: 'Share Market' }
]

export { footerLinks, topLinks }
