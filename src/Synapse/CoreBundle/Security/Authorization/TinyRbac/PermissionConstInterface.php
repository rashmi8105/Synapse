<?php
namespace Synapse\CoreBundle\Security\Authorization\TinyRbac;

/**
 * Implement interface to define constant for the permission set.
 *
 * @author Preet raj 
 */
interface PermissionConstInterface
{
    const PERM_CONTACTS_PUBLIC_VIEW = 'log_contacts-public-view';

    const PERM_CONTACTS_PRIVATE_VIEW = 'log_contacts-private-view';
    
    const PERM_CONTACTS_PUBLIC_CREATE = 'log_contacts-public-create';    

    const PERM_CONTACTS_PRIVATE_CREATE = 'log_contacts-private-create';

    const PERM_CONTACTS_TEAMS_VIEW = 'log_contacts-teams-view';

    const PERM_CONTACTS_TEAMS_CREATE = 'log_contacts-teams-create';
    
    const PERM_REFERRALS_PRIVATE_CREATE = 'referrals-private-create';
    
    const PERM_REFERRALS_PRIVATE_VIEW = 'referrals-private-view';
    
    const PERM_REFERRALS_PUBLIC_CREATE = 'referrals-public-create';
    
    const PERM_REFERRALS_PUBLIC_VIEW = 'referrals-public-view';
    
    const PERM_REFERRALS_RECEIVE = 'receive_referrals';
    
    const PERM_REFERRALS_TEAMS_CREATE = 'referrals-teams-create';
    
    const PERM_REFERRALS_TEAMS_VIEW = 'referrals-teams-view';
    
    const PERM_REASON_REFERRALS_PRIVATE_CREATE = 'reason-routed-referrals-private-create';
    
    const PERM_REASON_REFERRALS_PRIVATE_VIEW = 'reason-routed-referrals-private-view';
    
    const PERM_REASON_REFERRALS_PUBLIC_CREATE = 'reason-routed-referrals-public-create';
    
    const PERM_REASON_REFERRALS_PUBLIC_VIEW = 'reason-routed-referrals-public-view';
    
    const PERM_REASON_REFERRALS_TEAMS_CREATE = 'reason-routed-referrals-teams-create';
    
    const PERM_REASON_REFERRALS_TEAMS_VIEW = 'reason-routed-referrals-teams-view';
        
    const PERM_NOTES_PRIVATE_CREATE = 'notes-private-create';
    
    const PERM_NOTES_PRIVATE_VIEW = 'notes-private-view';
    
    const PERM_NOTES_PUBLIC_CREATE = 'notes-public-create';
    
    const PERM_NOTES_PUBLIC_VIEW = 'notes-public-view';
    
    const PERM_NOTES_TEAMS_CREATE = 'notes-teams-create';
    
    const PERM_NOTES_TEAMS_VIEW = 'notes-teams-view';

    const PERM_BOOKING_PRIVATE_CREATE = 'booking-private-create';
        
    const PERM_BOOKING_PRIVATE_VIEW = 'booking-private-view';
    
    const PERM_BOOKING_PUBLIC_CREATE = 'booking-public-create';
    
    const PERM_BOOKING_PUBLIC_VIEW = 'booking-public-view';
    
    const PERM_BOOKING_TEAMS_CREATE = 'booking-teams-create';
    
    const PERM_BOOKING_TEAMS_VIEW = 'booking-teams-view';
    
    const CONTACT_VIEW_EXCEPTION = 'contact';
    
    const NOTE_VIEW_EXCEPTION = 'note';
    
    const REFERRAL_VIEW_EXCEPTION = 'referral';
    
    const PERM_COORINDATOR_SETUP = 'coordinator-setup';
    
    const PERM_INDIVIDUALANDAGGREGATE = 'individualAndAggregate';
    
    const ASSET_REFERRALS = 'referrals';
    
    const ASSET_NOTES = 'notes';
    
    const ASSET_BOOKING = 'booking';
    
    const ASSET_CONTACTS = 'log_contacts';
    
    const ASSET_EMAIL = 'email';
    
    const PERM_EMAIL_PUBLIC_VIEW = 'email-public-view';
    
    const PERM_EMAIL_PRIVATE_VIEW = 'email-private-view';
    
    const PERM_EMAIL_PUBLIC_CREATE = 'email-public-create';
    
    const PERM_EMAIL_PRIVATE_CREATE = 'email-private-create';
    
    const PERM_EMAIL_TEAMS_VIEW = 'email-teams-view';
    
    const PERM_EMAIL_TEAMS_CREATE = 'email-teams-create';

}