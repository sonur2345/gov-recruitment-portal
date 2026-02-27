<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        h2 { font-size: 14px; margin: 16px 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .muted { color: #666; font-size: 11px; margin-bottom: 8px; }
    </style>
</head>
<body>
    <h1>Reports & Analytics Export</h1>
    <div class="muted">
        Generated at {{ now()->format('d M Y H:i') }}
        | Filters:
        Post={{ $filters['post_id'] ?? 'All' }},
        Category={{ $filters['category'] ?? 'All' }},
        Date From={{ $filters['date_from'] ?? 'N/A' }},
        Date To={{ $filters['date_to'] ?? 'N/A' }}
    </div>

    <h2>Summary</h2>
    <table>
        <tr><th>Total Posts</th><td>{{ $summary['total_posts'] }}</td><th>Total Applications</th><td>{{ $summary['total_applications'] }}</td></tr>
        <tr><th>Eligible</th><td>{{ $summary['eligible_candidates'] }}</td><th>Shortlisted</th><td>{{ $summary['shortlisted'] }}</td></tr>
        <tr><th>Qualified</th><td>{{ $summary['qualified'] }}</td><th>Selected</th><td>{{ $summary['selected'] }}</td></tr>
        <tr><th>Final Selected</th><td>{{ $summary['final_selected'] }}</td><th>Rejected</th><td>{{ $summary['rejected'] }}</td></tr>
        <tr><th>Waiting List</th><td>{{ $summary['waiting_list_count'] }}</td><th></th><td></td></tr>
    </table>

    <h2>Post-wise Statistics</h2>
    <table>
        <thead>
            <tr>
                <th>Post</th>
                <th>Applications</th>
                <th>Selected</th>
                <th>Waiting</th>
                <th>Rejected</th>
                <th>Vacancies</th>
                <th>Filled</th>
            </tr>
        </thead>
        <tbody>
            @forelse($postWiseStats as $row)
                <tr>
                    <td>{{ $row->post_label }}</td>
                    <td>{{ (int) $row->applications_count }}</td>
                    <td>{{ (int) $row->selected_count }}</td>
                    <td>{{ (int) $row->waiting_count }}</td>
                    <td>{{ (int) $row->rejected_count }}</td>
                    <td>{{ (int) $row->vacancies }}</td>
                    <td>{{ (int) $row->filled_seats }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No data available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Application Rows</h2>
    <table>
        <thead>
            <tr>
                <th>Application No</th>
                <th>Candidate</th>
                <th>Post</th>
                <th>Category</th>
                <th>Status</th>
                <th>Total Marks</th>
                <th>Rank</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['application_no'] }}</td>
                    <td>{{ $row['candidate_name'] }}</td>
                    <td>{{ $row['post'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['total_marks'] }}</td>
                    <td>{{ $row['rank'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
