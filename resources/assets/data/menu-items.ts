import {
    BIconAirplaneFill,
    BIconBell,
    BIconBookmarkHeart,
    BIconBuildingFill,
    BIconCarFrontFill,
    BIconGear,
    BIconGlobeAmericas,
    BIconGraphUpArrow,
    BIconHeart,
    BIconHouseDoor,
    BIconJournals,
    BIconPeople,
    BIconPerson,
    BIconStar,
    BIconTicketPerforated,
    BIconTrash,
    BIconWallet,
} from 'bootstrap-icons-vue';
import type { MenuItemType } from '../../js/Types/layout';

export const bookingHomeMenuItems: MenuItemType[] = [
    {
        key: 'hotels-home',
        label: 'Hotel',
        link: { name: 'home' },
        icon: BIconBuildingFill,
    },
    {
        key: 'flights-home',
        label: 'Flight',
        link: { name: 'home' },
        icon: BIconAirplaneFill,
    },
    {
        key: 'tour-home',
        label: 'Tour',
        link: { name: 'home' },
        icon: BIconGlobeAmericas,
    },
    {
        key: 'cabs-home',
        label: 'Cab',
        link: { name: 'home' },
        icon: BIconCarFrontFill,
    },
];

export const USER_PROFILE_MENU_ITEMS: MenuItemType[] = [
    {
        key: 'acc-user-profile',
        label: 'My Profile',
        link: { name: 'home' },
        parentKey: 'acc-user',
        icon: BIconPerson,
    },
    {
        key: 'acc-user-bookings',
        label: 'My Bookings',
        link: { name: 'home' },
        parentKey: 'acc-user',
        icon: BIconTicketPerforated,
    },
    {
        key: 'acc-user-travelers',
        label: 'Travelers',
        link: { name: 'home' },
        parentKey: 'acc-user',
        icon: BIconPeople,
    },
    {
        key: 'acc-user-payment-details',
        label: 'Payment Details',
        link: { name: 'home' },
        parentKey: 'acc-user',
        icon: BIconWallet,
    },
    {
        key: 'acc-user-wishlist',
        label: 'Wishlist',
        link: { name: 'home' },
        parentKey: 'acc-user',
        icon: BIconHeart,
    },
    {
        key: 'acc-user-settings',
        label: 'Settings',
        link: { name: 'home' },
        parentKey: 'acc-user',
        icon: BIconGear,
    },
    {
        key: 'acc-user-delete',
        label: 'Delete Profile',
        link: { name: 'home' },
        parentKey: 'acc-user',
        icon: BIconTrash,
    },
];

export const AGENT_MENU_ITEMS: MenuItemType[] = [
    {
        key: 'acc-dashboard',
        label: 'Dashboard',
        link: { name: 'home' },
        parentKey: 'acc-agent',
        icon: BIconHouseDoor,
    },
    {
        key: 'acc-agent-listings',
        label: 'Listings',
        link: { name: 'home' },
        parentKey: 'acc-agent',
        icon: BIconJournals,
    },
    {
        key: 'acc-agent-bookings',
        label: 'Bookings',
        link: { name: 'home' },
        parentKey: 'acc-agent',
        icon: BIconBookmarkHeart,
    },
    {
        key: 'acc-agent-activities',
        label: 'Activities',
        link: { name: 'home' },
        parentKey: 'acc-agent',
        icon: BIconBell,
    },
    {
        key: 'acc-agent-earnings',
        label: 'Earnings',
        link: { name: 'home' },
        parentKey: 'acc-agent',
        icon: BIconGraphUpArrow,
    },
    {
        key: 'acc-agent-reviews',
        label: 'Reviews',
        link: { name: 'home' },
        parentKey: 'acc-agent',
        icon: BIconStar,
    },
    {
        key: 'acc-agent-settings',
        label: 'Settings',
        link: { name: 'home' },
        parentKey: 'acc-agent',
        icon: BIconGear,
    },
];

export const ADMIN_MENU_ITEMS: MenuItemType[] = [
    {
        key: 'dashboard',
        label: 'Dashboard',
        link: { name: 'home' },
    },
    {
        key: 'bookings',
        label: 'Bookings',
        children: [
            {
                key: 'bookings-list',
                label: 'Booking List',
                link: { name: 'home' },
                parentKey: 'bookings',
            },
            {
                key: 'bookings-detail',
                label: 'Booking Detail',
                link: { name: 'home' },
                parentKey: 'bookings',
            },
        ],
    },
    {
        key: 'guests',
        label: 'Guests',
        children: [
            {
                key: 'guests-list',
                label: 'Guest List',
                link: { name: 'home' },
                parentKey: 'guests',
            },
            {
                key: 'guests-detail',
                label: 'Guest Detail',
                link: { name: 'home' },
                parentKey: 'guests',
            },
        ],
    },
    {
        key: 'agents',
        label: 'Agents',
        children: [
            {
                key: 'agents-list',
                label: 'Agent List',
                link: { name: 'home' },
                parentKey: 'agents',
            },
            {
                key: 'agents-detail',
                label: 'Agent Detail',
                link: { name: 'home' },
                parentKey: 'agents',
            },
        ],
    },
    {
        key: 'reviews',
        label: 'Reviews',
        link: { name: 'home' },
    },
    {
        key: 'earnings',
        label: 'Earnings',
        link: { name: 'home' },
    },
    {
        key: 'admin-settings',
        label: 'Admin Settings',
        link: { name: 'home' },
    },
    {
        key: 'admin-auth',
        label: 'Authentication',
        children: [
            {
                key: 'auth-sign-up',
                label: 'Sign Up',
                link: { name: 'home' },
                parentKey: 'admin-auth',
            },
            {
                key: 'auth-sign-in',
                label: 'Sign in',
                link: { name: 'home' },
                parentKey: 'admin-auth',
            },
            {
                key: 'auth-forgot-password',
                label: 'Forgot Password',
                link: { name: 'home' },
                parentKey: 'admin-auth',
            },
            {
                key: 'auth-two-factor-authentication',
                label: 'Two Factor Authentication',
                link: { name: 'home' },
                parentKey: 'admin-auth',
            },
            {
                key: 'auth-not-found',
                label: 'Error 404',
                link: { name: 'home' },
                parentKey: 'admin-auth',
            },
        ],
    },
];

export const HELP_MENU_ITEMS: MenuItemType[] = [
    {
        key: 'help-center',
        label: 'Help Center',
        isTitle: true,
        children: [
            {
                key: 'helps-center-page',
                label: 'Help Center',
                link: { name: 'home' },
                parentKey: 'help-center',
            },
            {
                key: 'helps-detail-page',
                label: 'Help Detail',
                link: { name: 'home' },
                parentKey: 'help-center',
            },
        ],
    },
    {
        key: 'helps-privacy-policy',
        label: 'Privacy Policy',
        link: { name: 'home' },
        isTitle: true,
    },
    {
        key: 'helps-service',
        label: 'Terms of Service',
        link: { name: 'home' },
        isTitle: true,
    },
];

export const APP_MENU_ITEMS: MenuItemType[] = [
    {
        key: 'search',
        label: 'Recherche personnalisée',
        link: { name: 'custom-search.index' },
    },
    {
        key: 'hotel-home',
        label: 'Attestation de logement',
        link: { name: 'certificate.home' },
        parentKey: 'hotels',
    },
];
