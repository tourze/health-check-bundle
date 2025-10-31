<?php

declare(strict_types=1);

namespace HealthCheckBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * SQL健康检查管理控制器
 *
 * @extends AbstractCrudController<SqlChecker>
 */
#[AdminCrud(routePath: '/health-check/sql-checker', routeName: 'health_check_sql_checker')]
#[IsGranted(attribute: 'ROLE_ADMIN')]
final class SqlCheckerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SqlChecker::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SQL健康检查')
            ->setEntityLabelInPlural('SQL健康检查')
            ->setPageTitle('index', 'SQL健康检查列表')
            ->setPageTitle('detail', 'SQL健康检查详情')
            ->setPageTitle('new', '新建SQL健康检查')
            ->setPageTitle('edit', '编辑SQL健康检查')
            ->setHelp('index', '管理数据库SQL查询健康检查配置')
            ->setDefaultSort(['valid' => 'DESC', 'updateTime' => 'DESC'])
            ->setSearchFields(['name', 'sql', 'cronExpression', 'remark'])
            ->setPaginatorPageSize(20)
        ;
    }

    /**
     * @return iterable<FieldInterface|string>
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('name', '检查名称')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('健康检查的名称标识')
        ;

        yield TextareaField::new('sql', '检查SQL')
            ->setRequired(true)
            ->setHelp('用于健康检查的SQL查询语句，查询结果将与对比值进行比较')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => ['rows' => 6],
            ])
        ;

        yield TextField::new('cronExpression', 'Cron表达式')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('定时执行的Cron表达式，如：0 */5 * * * *（每5分钟）')
        ;

        $operatorField = EnumField::new('operator', '操作符');
        $operatorField->setEnumCases(SqlOperatorEnum::cases());
        $operatorField->setRequired(true);
        $operatorField->setHelp('SQL查询结果与对比值的比较操作符');
        $operatorField->renderAsBadges([
            '>' => 'success',
            '>=' => 'success',
            '<' => 'warning',
            '<=' => 'warning',
            '=' => 'primary',
            '!=' => 'danger',
        ]);
        yield $operatorField;

        yield IntegerField::new('compareValue', '对比值')
            ->setRequired(true)
            ->setHelp('与SQL查询结果进行比较的数值')
            ->setFormTypeOption('attr', ['min' => 0])
        ;

        yield TextareaField::new('remark', '备注')
            ->setRequired(false)
            ->setMaxLength(1000)
            ->setHelp('检查配置的说明或备注信息')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => ['rows' => 3],
            ])
        ;

        yield BooleanField::new('valid', '有效')
            ->setHelp('是否启用此健康检查')
        ;

        yield TextField::new('createdBy', '创建人')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '检查名称'))
            ->add(ChoiceFilter::new('operator', '操作符')
                ->setChoices($this->getOperatorChoices()))
            ->add(BooleanFilter::new('valid', '有效'))
            ->add(TextFilter::new('createdBy', '创建人'))
            ->add(TextFilter::new('updatedBy', '更新人'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }

    public function createEntity(string $entityFqcn): SqlChecker
    {
        $checker = new SqlChecker();
        $checker->setName('新健康检查');
        $checker->setSql('SELECT 1');
        $checker->setCronExpression('0 * * * * *');
        $checker->setOperator(SqlOperatorEnum::EQ);
        $checker->setCompareValue(0);
        $checker->setValid(true);

        return $checker;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * 获取操作符选项
     * @return array<string, string>
     */
    private function getOperatorChoices(): array
    {
        $choices = [];
        foreach (SqlOperatorEnum::cases() as $operator) {
            $choices[$operator->getLabel()] = $operator->value;
        }

        return $choices;
    }
}
