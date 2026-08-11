@component('mail::message')
# Welcome to the Matter Mind Post-Handover Maintenance Portal

Dear {{ $client->contact_name ?: 'Customer' }},

Thank you for choosing Matter Mind.

We are pleased to inform you that your organization has been successfully onboarded to our Post-Handover Maintenance &amp; Service Management System.

@if ($project)
**Project Details**

@component('mail::table')
|                                     |                                                                                          |
| :---------------------------------- | :--------------------------------------------------------------------------------------- |
| **Project**                         | {{ $project->project_name ?: 'N/A' }}                                                     |
| **Location**                        | {{ $project->site_name ?: 'N/A' }}                                                        |
| **Shop Opening / Handover Date**    | {{ optional($project->completion_date)->format('d M Y') ?? 'To be confirmed' }}           |
| **Warranty Expiry**                 | {{ optional($project->warranty_end_date)->format('d M Y') ?? 'To be confirmed' }}         |
@endcomponent
@endif

Through this platform, we will manage all future maintenance and service requests in a structured and transparent manner.

### What you can expect

- Dedicated Service Request Number for every request
- WhatsApp and email updates throughout the service process
- Engineer visit scheduling and notifications
- Status updates from request creation until completion
- Service history and maintenance records
- Faster response and improved communication
- Clear distinction between warranty and post-warranty services

@if ($portalUrl)
@component('mail::button', ['url' => $portalUrl])
View Your Projects
@endcomponent

This link is private to your organization — please keep it safe.
@endif

If your organization would like additional members of your team to receive service updates, please let our team know and we will add them to the notification list.

We look forward to supporting you and providing the highest level of after-sales service.

Kind Regards,<br>
**Matter Mind Decor &amp; General Maintenance LLC**<br>
Post-Handover Maintenance Team
@endcomponent