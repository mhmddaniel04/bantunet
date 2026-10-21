{{-- Report content shared by the screen page and the print page. Needs: $report --}}
@php
    $s = $report['summary'];
    $pct = fn (?float $v) => $v === null ? '—' : rtrim(rtrim(number_format($v, 1), '0'), '.').'%';
@endphp

<div class="stats">
    <x-stat-card label="Initiatives held" :value="$s['initiatives']" hint="In this period" />
    <x-stat-card label="Applications" :value="$s['applications']" hint="Received" />
    <x-stat-card label="Volunteers" :value="$s['volunteers']" hint="Unique people approved" />
    <x-stat-card label="Volunteer hours" :value="rtrim(rtrim(number_format($s['hours'], 1), '0'), '.')" hint="From attendance records" />
    <x-stat-card label="Approval rate" :value="$pct($s['approval_rate'])" hint="Approved ÷ reviewed" />
    <x-stat-card label="Attendance rate" :value="$pct($s['attendance_rate'])" hint="Attended ÷ approved (past events)" />
    <x-stat-card label="Via Smart Match" :value="$pct($s['smart_match_share'])" hint="Share of applications" />
    <x-stat-card label="Avg. match score" :value="$pct($s['avg_match_score'])" hint="Smart Match applications" />
</div>

<div class="report-grid report-grid--2">
    <section class="card">
        <h2 class="card__title">Volunteers per month</h2>
        <p class="card__subtitle">Approved volunteers, by event date</p>
        <x-bar-chart :items="collect($report['monthly'])->map(fn ($m) => ['label' => $m['label'], 'value' => $m['approved']])->all()" />
    </section>

    <section class="card">
        <h2 class="card__title">Volunteer hours per month</h2>
        <p class="card__subtitle">Hours from attendance records</p>
        <x-bar-chart dark :items="collect($report['monthly'])->map(fn ($m) => ['label' => $m['label'], 'value' => round($m['hours'], 1)])->all()" />
    </section>
</div>

<div class="report-grid report-grid--2">
    <section class="card">
        <h2 class="card__title">Smart Match vs Browse</h2>
        <p class="card__subtitle">Approval rate of applications from each source</p>
        <x-bar-list suffix="%" :max="100"
                    :items="collect($report['sources'])->map(fn ($r) => ['label' => $r['label'].' ('.$r['applications'].')', 'value' => $r['approval_rate']])->all()" />
    </section>

    <section class="card">
        <h2 class="card__title">By cause</h2>
        <p class="card__subtitle">Approved volunteers per category</p>
        <x-bar-list :items="collect($report['categories'])->map(fn ($c) => ['label' => $c['label'], 'value' => $c['approved']])->all()" />
    </section>

    <section class="card">
        <h2 class="card__title">Gender</h2>
        <p class="card__subtitle">Approved volunteers</p>
        <x-bar-list :items="$report['genders']" />
    </section>

    <section class="card">
        <h2 class="card__title">Age groups</h2>
        <p class="card__subtitle">Approved volunteers</p>
        <x-bar-list :items="$report['age_groups']" />
    </section>
</div>

<section class="card table-card" style="margin-top: 24px;">
    <div style="padding: 24px 24px 0;">
        <h2 class="card__title">Initiatives</h2>
    </div>
    <div class="table-wrap" style="margin-top: 16px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Initiative</th>
                    <th>Date</th>
                    <th class="text-right">Applications</th>
                    <th class="text-right">Approved</th>
                    <th class="text-right">Attended</th>
                    <th class="text-right">Hours</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report['initiatives'] as $row)
                    <tr>
                        <td>
                            <p class="table__title">{{ $row['event']->title }}</p>
                            <p class="table__sub">{{ $row['event']->category->name }} · {{ $row['event']->state }}</p>
                        </td>
                        <td>{{ $row['event']->event_date->format('j M Y') }}</td>
                        <td class="text-right">{{ $row['applications'] }}</td>
                        <td class="text-right">{{ $row['approved'] }}</td>
                        <td class="text-right">{{ $row['attended'] }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($row['hours'], 1), '0'), '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">No initiatives in this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>