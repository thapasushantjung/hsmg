import { Head } from '@inertiajs/react';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { dashboard } from '@/routes';
import { getTodayBS } from '@/lib/nepali-date';

export default function Dashboard() {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between pb-2 border-b">
                    <h1 className="text-2xl font-bold tracking-tight">Overview</h1>
                    <div className="flex items-center gap-2 text-muted-foreground font-medium">
                        <span className="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm">
                            Today: {getTodayBS()} BS
                        </span>
                    </div>
                </div>

                <div className="grid auto-rows-min gap-4 md:grid-cols-3">


                </div>

            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
