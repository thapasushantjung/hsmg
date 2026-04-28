import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import InputError from '@/components/input-error';
import calendar from '@/routes/calendar';
import { getTodayBS } from '@/lib/nepali-date';

const nepaliMonths = [
    'Baisakh', 'Jestha', 'Ashadh', 'Shrawan', 'Bhadra', 'Ashwin',
    'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'
];

interface CalendarMap {
    id: number;
    year: number;
    months: number[];
}

export default function CalendarSettings({ maps }: { maps: CalendarMap[] }) {
    const [editingId, setEditingId] = useState<number | null>(null);

    const { data, setData, post, put, processing, errors, reset, clearErrors } = useForm({
        year: new Date().getFullYear() + 57 + 1, // Default to roughly next BS year
        months: [31, 31, 31, 32, 31, 30, 30, 30, 29, 30, 29, 30] // Default safe values
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        
        if (editingId) {
            put(calendar.update({ calendarMap: editingId }).url, {
                preserveScroll: true,
                onSuccess: () => {
                    reset();
                    setEditingId(null);
                },
            });
        } else {
            post(calendar.store().url, {
                preserveScroll: true,
                onSuccess: () => reset(),
            });
        }
    };

    const handleEdit = (map: CalendarMap) => {
        setEditingId(map.id);
        setData({
            year: map.year,
            months: map.months,
        });
        clearErrors();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleCancel = () => {
        setEditingId(null);
        reset();
        clearErrors();
    };

    const handleMonthChange = (index: number, value: string) => {
        const newMonths = [...data.months];
        newMonths[index] = parseInt(value) || 0;
        setData('months', newMonths);
    };

    return (
        <>
            <Head title="Calendar Settings" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between pb-2 border-b">
                    <h1 className="text-2xl font-bold tracking-tight">Calendar Settings</h1>
                    <div className="flex items-center gap-2 text-muted-foreground font-medium">
                        <span className="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm">
                            Today: {getTodayBS()} BS
                        </span>
                    </div>
                </div>

                <div className="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{editingId ? 'Edit Calendar Year' : 'Add New Calendar Year'}</CardTitle>
                            <CardDescription>
                                Define the exact number of days for each month in an upcoming Bikram Sambat year.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={submit} className="space-y-6">
                                <div className="space-y-2 max-w-xs">
                                    <Label htmlFor="year">BS Year</Label>
                                    <Input
                                        id="year"
                                        type="number"
                                        value={data.year}
                                        onChange={(e) => setData('year', parseInt(e.target.value) || 0)}
                                    />
                                    <InputError message={errors.year} />
                                </div>

                                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    {nepaliMonths.map((month, index) => (
                                        <div key={month} className="space-y-2">
                                            <Label htmlFor={`month-${index}`}>{month}</Label>
                                            <Input
                                                id={`month-${index}`}
                                                type="number"
                                                min="28"
                                                max="32"
                                                value={data.months[index]}
                                                onChange={(e) => handleMonthChange(index, e.target.value)}
                                            />
                                            <InputError message={(errors as any)[`months.${index}`]} />
                                        </div>
                                    ))}
                                </div>
                                <InputError message={errors.months} />

                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        {editingId ? 'Update Calendar Year' : 'Save Calendar Year'}
                                    </Button>
                                    {editingId && (
                                        <Button type="button" variant="outline" onClick={handleCancel}>
                                            Cancel Edit
                                        </Button>
                                    )}
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Existing Mapped Years</CardTitle>
                            <CardDescription>The most recently added calendar definitions.</CardDescription>
                        </CardHeader>
                        <CardContent className="p-0">
                            <div className="overflow-x-auto rounded-b-md border-t">
                                <table className="w-full text-sm text-left whitespace-nowrap">
                                    <thead className="bg-muted/50 border-b">
                                        <tr>
                                            <th className="px-4 py-3 font-medium">BS Year</th>
                                            <th className="px-4 py-3 font-medium">Total Days</th>
                                            <th className="px-4 py-3 font-medium">Baisakh...Chaitra</th>
                                            <th className="px-4 py-3 font-medium text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {maps.map((map) => (
                                            <tr key={map.id} className="border-b last:border-0">
                                                <td className="px-4 py-3 font-medium">{map.year}</td>
                                                <td className="px-4 py-3 text-muted-foreground">
                                                    {map.months.reduce((a, b) => a + b, 0)} days
                                                </td>
                                                <td className="px-4 py-3 font-mono text-xs text-muted-foreground">
                                                    [{map.months.join(', ')}]
                                                </td>
                                                <td className="px-4 py-3 text-right">
                                                    <Button variant="ghost" size="sm" onClick={() => handleEdit(map)}>
                                                        Edit
                                                    </Button>
                                                </td>
                                            </tr>
                                        ))}
                                        {maps.length === 0 && (
                                            <tr>
                                                <td colSpan={4} className="px-4 py-8 text-center text-muted-foreground italic">
                                                    No calendar definitions found.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

CalendarSettings.layout = {
    breadcrumbs: [
        {
            title: 'Calendar Settings',
            href: calendar.index(),
        },
    ],
};
