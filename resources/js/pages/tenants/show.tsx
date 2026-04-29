import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { User, Phone, MapPin, Calendar, Activity, Home } from 'lucide-react';
import { tenants } from '@/routes';

interface TenantProfile {
    id: number;
    full_name: string;
    first_name: string;
    last_name: string;
    gender: string;
    phone: string;
    status: string;
    date_of_birth?: string;
    blood_group?: string;
    emergency_contact_name?: string;
    emergency_contact_phone?: string;
    active_booking?: {
        status: string;
        check_in_date: string;
        current_bed_assignment?: {
            started_at: string;
            bed?: {
                name: string;
                room?: {
                    name: string;
                    floor?: {
                        name: string;
                    }
                }
            }
        }
    };
}

export default function ShowTenant({ tenant }: { tenant: TenantProfile }) {
    const currentBed = tenant.active_booking?.current_bed_assignment?.bed;
    const room = currentBed?.room;
    const floor = room?.floor;

    return (
        <>
            <Head title={`${tenant.full_name} - Profile`} />
            <div className="flex flex-1 flex-col gap-4 p-4 lg:p-8 max-w-4xl mx-auto w-full">
                <div className="flex justify-between items-center">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">{tenant.full_name}</h1>
                        <p className="text-sm text-muted-foreground">Tenant Profile</p>
                    </div>
                    <Button variant="outline" asChild>
                        <Link href={tenants()}>Back to Tenants</Link>
                    </Button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card className="md:col-span-1">
                        <CardHeader>
                            <div className="flex items-center gap-4">
                                <div className="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl">
                                    {tenant.first_name.charAt(0)}{tenant.last_name.charAt(0)}
                                </div>
                                <div>
                                    <CardTitle>{tenant.full_name}</CardTitle>
                                    <Badge variant={tenant.status === 'active' ? 'default' : 'secondary'} className="mt-1">
                                        {tenant.status.toUpperCase()}
                                    </Badge>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="flex items-center gap-2 text-sm">
                                <Phone className="h-4 w-4 text-muted-foreground" />
                                <span>{tenant.phone}</span>
                            </div>
                            <div className="flex items-center gap-2 text-sm">
                                <User className="h-4 w-4 text-muted-foreground" />
                                <span className="capitalize">{tenant.gender}</span>
                            </div>
                            {tenant.date_of_birth && (
                                <div className="flex items-center gap-2 text-sm">
                                    <Calendar className="h-4 w-4 text-muted-foreground" />
                                    <span>{tenant.date_of_birth}</span>
                                </div>
                            )}
                            {tenant.blood_group && (
                                <div className="flex items-center gap-2 text-sm">
                                    <Activity className="h-4 w-4 text-muted-foreground" />
                                    <span>{tenant.blood_group}</span>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <div className="md:col-span-2 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-lg flex items-center gap-2">
                                    <Home className="h-5 w-5" /> Current Accommodation
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {tenant.active_booking ? (
                                    <div className="space-y-2">
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <p className="text-sm text-muted-foreground">Status</p>
                                                <Badge variant="outline" className="bg-emerald-50 text-emerald-700 border-emerald-200">
                                                    Checked In
                                                </Badge>
                                            </div>
                                            <div>
                                                <p className="text-sm text-muted-foreground">Check-in Date</p>
                                                <p className="font-medium">{tenant.active_booking.check_in_date}</p>
                                            </div>
                                            {currentBed && (
                                                <>
                                                    <div>
                                                        <p className="text-sm text-muted-foreground">Bed</p>
                                                        <p className="font-medium">{currentBed.name}</p>
                                                    </div>
                                                    <div>
                                                        <p className="text-sm text-muted-foreground">Room & Floor</p>
                                                        <p className="font-medium">{room?.name} ({floor?.name})</p>
                                                    </div>
                                                </>
                                            )}
                                        </div>
                                    </div>
                                ) : (
                                    <p className="text-sm text-muted-foreground">No active booking for this tenant.</p>
                                )}
                            </CardContent>
                        </Card>

                        {(tenant.emergency_contact_name || tenant.emergency_contact_phone) && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg flex items-center gap-2">
                                        <Activity className="h-5 w-5 text-destructive" /> Emergency Contact
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-sm text-muted-foreground">Name</p>
                                            <p className="font-medium">{tenant.emergency_contact_name || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">Phone</p>
                                            <p className="font-medium">{tenant.emergency_contact_phone || 'N/A'}</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

ShowTenant.layout = {
    breadcrumbs: [
        {
            title: 'Tenants',
            href: tenants(),
        },
        {
            title: 'Profile',
            href: '#',
        },
    ],
};
