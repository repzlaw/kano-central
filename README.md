# KANO Central

KANO Central is a comprehensive student portal designed to facilitate student and admin interactions within an academic environment. The portal leverages [Filament Jetstream](https://github.com/stephenjude/filament-jetstream) for a seamless user experience, offering robust features for both students and administrators.

## Features

### User Interface

- **Home Page & Login Page**
  - Utilizes Jetstream for authentication.
  - No registration option; all users must be added by an admin.

- **User Profile Management**
  - Powered by Jetstream.

- **User Dashboard**
  - Built with Filament.
  - **Panels:**
    - Required Tasks
    - Activity Hours Year to Date
    - Event Calendar
      - Displays student-specific events.
      - Displays global events based on student attributes (e.g., specific program alumni events, all student events).

- **Submit Activities**
  - Added activities create an admin task and require admin review/approval.
  - Admins are required to review every activity document.

- **Submit Credentials**
  - Added credentials create an admin task and require admin review/approval.
  - Admins are required to review every credential document.

### Admin Dashboard

- Built with Filament.
- **Panels:**
  - Number of Registered Users
  - Number of Active Users Past 30 Days
  - Required Tasks
- **CRUD Operations:**
  - Users
  - Groups
  - Activities
  - Credentials
  - Events
    - Add/Edit supports maintaining assignments of events to one or more students.
  - Tasks
    - Add/Edit supports maintaining assignments of tasks to one or more students/admins.

## Requirements

- [Filament Jetstream](https://github.com/stephenjude/filament-jetstream)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/kano-central.git
