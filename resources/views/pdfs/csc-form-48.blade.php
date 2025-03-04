<style>
    @page {
        size: A4 portrait; /* Ensure A4 portrait */
        margin: 0; /* Remove all margins */
    }
    body {
        margin: 10px 10px;
        padding: 0;
    }
    table {
        padding: 10px;
    }
</style>
<table>
    <tr>
        <td>
            <table style="padding: 10px 0px; border-spacing: 0px; border-collapse: collapse; background-color: white; color: black; font-size:11px; font-family: Calibri">
                <tr style="height: 10px"><td colspan="7" style="text-align: left; font-size: 10px; font-style: italic">Civil Service Form No. 48</td></tr>
                <tr style="height: 10px"><th colspan="7" style="text-align: center; font-weight: bold; font-size: 16px">DAILY <span style="border-bottom: 3px solid black;">TIME</span> RECORD</th></tr>
                <tr style="height: 18px; border-bottom: 1px dashed black">
                    <td colspan="7" style="text-align: center; padding: 0">{{ $userName }}</td>
                </tr>
                <tr style="height: 10px">
                    <td colspan="7" style="text-align: center; font-style: italic; font-weight:bold">(Name)</td>
                </tr>
                <tr style="height: 10px">
                    <td colspan="2" style="text-align: left; font-style: italic">For the month of</td>
                    <td colspan="3" style="text-align: center; font-style: italic; border-bottom: 1px solid black">
                        {{ $monthName }}
                    </td>
                    <td colspan="1" style="text-align: right; font-style: italic"></td>
                    <td colspan="1" style="text-align: center; font-style: italic; border-bottom: 1px solid black">{{ $year }}</td>
                </tr>
                <tr style="height: 10px">
                    <td colspan="3"><span style="text-align: left; font-style: italic">Official hours of arrival</span></td>
                    <td colspan="2" style="text-align: right; font-style: italic">Regular Days</td>
                    <td colspan="2" style="text-align: left; font-style: italic; border-bottom: 1px solid black"></td>
                </tr>
                <tr style="height: 10px">
                    <td colspan="1" style="text-align: right; font-style: italic"></td>
                    <td colspan="2" style="text-align: left; font-style: italic">and departure</td>
                    <td colspan="2" style="text-align: right; font-style: italic">Saturdays</td>
                    <td colspan="2" style="text-align: left; font-style: italic; border-bottom: 1px solid black"></td>
                </tr>
                <tr style="height: 10px"></tr>
                <tr style="border-top: 3px solid black; height: 10px">
                    <th rowspan="2" style="border-right: 2px solid black; border-bottom: 2px solid black">Days</th>
                    <th colspan="2" style="border-right: 6px double black">A.M.</th>
                    <th colspan="2" style="border-right: 6px double black">P.M.</th>
                    <th colspan="2">UNDERTIME</th>
                </tr>
                <tr style="border-top: 2px solid black; height: 10px">
                    <td style="width: 50px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 2px solid black; border-bottom: 2px solid black">ARRIVAL</td>
                    <td style="width: 50px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 6px double black; border-bottom: 2px solid black">DEPARTURE</td>
                    <td style="width: 50px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 2px solid black; border-bottom: 2px solid black">ARRIVAL</td>
                    <td style="width: 50px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 6px double black; border-bottom: 2px solid black">DEPARTURE</td>
                    <td style="width: 45px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 2px solid black; border-bottom: 2px solid black">Hours</td>
                    <td style="width: 45px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-bottom: 2px solid black">Minutes</td>
                </tr>
                <!-- Generate rows for days -->
                @for ($i = 1; $i <= 31; $i++)
                    @php
                        $attendance = collect($dtrData)->firstWhere('day', $i);
                    @endphp
                    <tr style="background-color: white; color: black; height: 10px;">
                        <td style="text-align: right; padding-right: 2px;">{{ $i }}</td>
                        <td style="border-right: 2px solid black; border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['time_in_am'] ?? '' }}</td>
                        <td style="border-right: 6px double black; border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['time_out_am'] ?? '' }}</td>
                        <td style="border-right: 2px solid black; border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['time_in_pm'] ?? '' }}</td>
                        <td style="border-right: 6px double black; border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['time_out_pm'] ?? '' }}</td>
                        <td style="border-right: 2px solid black; border-bottom: 1px dashed black; border-left: 2px solid black;  text-align: center; padding: 0">{{ $attendance['undertime_hours'] ?? '' }}</td>
                        <td style="border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['undertime_minutes'] ?? '' }}</td>
                    </tr>
                @endfor
                <tr style="height: 4px"></tr>
                <tr style="height: 10px; border-top: 3px solid black">
                    <td colspan="1" style="text-align: left; font-weight: bold;"></td>
                    <td colspan="1" style="text-align: left; font-weight: bold;">TOTAL</td>
                    <td colspan="3" style="border-bottom: 2px solid black; border-right: 6px double black; text-align: left; font-weight: bold;"></td>
                    <td colspan="1" style="border-bottom: 2px solid black; border-right: 2px solid black; text-align: center; font-weight: bold;">{{ $totalUndertimeHours }}</td>
                    <td colspan="1" style="border-bottom: 2px solid black; text-align: center; font-weight: bold;">{{ $totalUndertimeMinutes }}</td>
                </tr>
                <tr style="height: 4px; border-bottom: 3px solid black;"></tr>
                <tr>
                    <td colspan="7" style="height: 33px; text-align: left; font-style: italic; margin-top: 10px; font-size: 11px">I CERTIFY on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.</td>
                </tr>
                <tr style="height: 11px;">
                    <td colspan="2" style="text-align: left; font-weight: bold;"></td>
                    <td colspan="5" style="border-bottom: 2px solid black; text-align: left; font-weight: bold; text-align: center;">{{ mb_strtoupper($userName) ?? '' }}</td>
                </tr>
                <tr style="height: 6px; border-bottom: 3px solid black;"></tr>
                <tr style="height: 50px;">
                    <td colspan="2" style="text-align: left; font-weight: bold;"></td>
                    <td colspan="5" style="border-bottom: 2px solid black; text-align: left; font-weight: bold;"></td>
                </tr>
                <tr style="height: 11px">
                    <td colspan="5"></td>
                    <td colspan="2" style="font-size: 12px; font-weight: bold; font-style: italic;">In-charge</td>
                </tr>
            </table>
        </td>
        <td style="width: 10px"></td>
        <td>
            <table style="border-spacing: 0px; border-collapse: collapse; width: 45%; background-color: white; color: black; font-size:11px; font-family: Calibri">
                <tr style="height: 10px"><td colspan="7" style="text-align: left; font-size: 10px; font-style: italic">Civil Service Form No. 48</td></tr>
                <tr style="height: 10px"><th colspan="7" style="text-align: center; font-weight: bold; font-size: 16px">DAILY <span style="border-bottom: 3px solid black;">TIME</span> RECORD</th></tr>
                <tr style="height: 18px; border-bottom: 1px dashed black">
                    <td colspan="7" style="text-align: center; padding: 0">{{ $userName }}</td>
                </tr>
                <tr style="height: 10px">
                    <td colspan="7" style="text-align: center; font-style: italic; font-weight:bold">(Name)</td>
                </tr>
                <tr style="height: 10px">
                    <td colspan="2" style="text-align: left; font-style: italic">For the month of</td>
                    <td colspan="3" style="text-align: center; font-style: italic; border-bottom: 1px solid black">
                        {{ $monthName }}
                    </td>
                    <td colspan="1" style="text-align: right; font-style: italic"></td>
                    <td colspan="1" style="text-align: center; font-style: italic; border-bottom: 1px solid black">{{ $year }}</td>
                </tr>
                <tr style="height: 10px">
                    <td colspan="3"><span style="text-align: left; font-style: italic">Official hours of arrival</span></td>
                    <td colspan="2" style="text-align: right; font-style: italic">Regular Days</td>
                    <td colspan="2" style="text-align: left; font-style: italic; border-bottom: 1px solid black"></td>
                </tr>
                <tr style="height: 10px">
                    <td colspan="1" style="text-align: right; font-style: italic"></td>
                    <td colspan="2" style="text-align: left; font-style: italic">and departure</td>
                    <td colspan="2" style="text-align: right; font-style: italic">Saturdays</td>
                    <td colspan="2" style="text-align: left; font-style: italic; border-bottom: 1px solid black"></td>
                </tr>
                <tr style="height: 10px"></tr>
                <tr style="border-top: 3px solid black; height: 10px">
                    <th rowspan="2" style="border-right: 2px solid black; border-bottom: 2px solid black">Days</th>
                    <th colspan="2" style="border-right: 6px double black">A.M.</th>
                    <th colspan="2" style="border-right: 6px double black">P.M.</th>
                    <th colspan="2">UNDERTIME</th>
                </tr>
                <tr style="border-top: 2px solid black; height: 10px">
                    <td style="width: 50px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 2px solid black; border-bottom: 2px solid black">ARRIVAL</td>
                    <td style="width: 50px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 6px double black; border-bottom: 2px solid black">DEPARTURE</td>
                    <td style="width: 50px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 2px solid black; border-bottom: 2px solid black">ARRIVAL</td>
                    <td style="width: 50px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 6px double black; border-bottom: 2px solid black">DEPARTURE</td>
                    <td style="width: 45px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-right: 2px solid black; border-bottom: 2px solid black">Hours</td>
                    <td style="width: 45px; font-size: 9px; text-align: center; font-weight: bold; font-style: italic; border-bottom: 2px solid black">Minutes</td>
                </tr>
                <!-- Generate rows for days -->
                @for ($i = 1; $i <= 31; $i++)
                    @php
                        $attendance = collect($dtrData)->firstWhere('day', $i);
                    @endphp
                    <tr style="background-color: white; color: black; height: 10px;">
                        <td style="text-align: right; padding-right: 2px;">{{ $i }}</td>
                        <td style="border-right: 2px solid black; border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['time_in_am'] ?? '' }}</td>
                        <td style="border-right: 6px double black; border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['time_out_am'] ?? '' }}</td>
                        <td style="border-right: 2px solid black; border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['time_in_pm'] ?? '' }}</td>
                        <td style="border-right: 6px double black; border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['time_out_pm'] ?? '' }}</td>
                        <td style="border-right: 2px solid black; border-bottom: 1px dashed black; border-left: 2px solid black;  text-align: center; padding: 0">{{ $attendance['undertime_hours'] ?? '' }}</td>
                        <td style="border-bottom: 1px dashed black; border-left: 2px solid black; text-align: center; padding: 0">{{ $attendance['undertime_minutes'] ?? '' }}</td>
                    </tr>
                @endfor
                <tr style="height: 4px"></tr>
                <tr style="height: 10px; border-top: 3px solid black">
                    <td colspan="1" style="text-align: left; font-weight: bold;"></td>
                    <td colspan="1" style="text-align: left; font-weight: bold;">TOTAL</td>
                    <td colspan="3" style="border-bottom: 2px solid black; border-right: 6px double black; text-align: left; font-weight: bold;"></td>
                    <td colspan="1" style="border-bottom: 2px solid black; border-right: 2px solid black; text-align: center; font-weight: bold;">{{ $totalUndertimeHours }}</td>
                    <td colspan="1" style="border-bottom: 2px solid black; text-align: center; font-weight: bold;">{{ $totalUndertimeMinutes }}</td>
                </tr>
                <tr style="height: 4px; border-bottom: 3px solid black;"></tr>
                <tr>
                    <td colspan="7" style="height: 33px; text-align: left; font-style: italic; margin-top: 10px; font-size: 11px">I CERTIFY on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.</td>
                </tr>
                <tr style="height: 11px;">
                    <td colspan="2" style="text-align: left; font-weight: bold;"></td>
                    <td colspan="5" style="border-bottom: 2px solid black; text-align: left; font-weight: bold; text-align: center;">{{ mb_strtoupper($userName) ?? '' }}</td>
                </tr>
                <tr style="height: 6px; border-bottom: 3px solid black;"></tr>
                <tr style="height: 50px;">
                    <td colspan="2" style="text-align: left; font-weight: bold;"></td>
                    <td colspan="5" style="border-bottom: 2px solid black; text-align: left; font-weight: bold;"></td>
                </tr>
                <tr style="height: 11px">
                    <td colspan="5"></td>
                    <td colspan="2" style="font-size: 12px; font-weight: bold; font-style: italic;">In-charge</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
